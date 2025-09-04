<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Order;
use App\ValueObjects\Payment\PaymentCallbackResult;
use App\Exceptions\Payment\PaymentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PayTR callback işleme servisi
 * PayTR'den gelen ödeme sonuç bildirimlerini güvenli şekilde işler
 */
class PayTrCallbackHandler
{
    private array $config;

    public function __construct()
    {
        $this->config = config('payments.providers.paytr', []);
    }

    /**
     * PayTR'den gelen callback'i işler
     * Hash doğrulaması yapar ve ödeme sonucunu döner
     * 
     * @param Request $request PayTR'den gelen callback request'i
     * @return PaymentCallbackResult İşlenmiş callback sonucu
     */
    public function handle(Request $request): PaymentCallbackResult
    {
        try {
            Log::info('PayTR callback alındı', [
                'ip' => $request->ip(),
                'method' => $request->method(),
                'has_post_data' => !empty($request->getContent())
            ]);

            // PayTR callback verilerini al
            $callbackData = $this->extractCallbackData($request);
            
            // Hash güvenlik doğrulaması yap
            if (!$this->validateCallbackHash($callbackData)) {
                Log::error('PayTR callback hash doğrulama başarısız', [
                    'merchant_oid' => $callbackData['merchant_oid'] ?? 'unknown',
                    'received_hash' => $callbackData['hash'] ?? 'missing'
                ]);
                
                return PaymentCallbackResult::failure(
                    orderNumber: $callbackData['merchant_oid'] ?? 'unknown',
                    errorMessage: 'PayTR callback hash doğrulama başarısız',
                    errorCode: 'INVALID_HASH'
                );
            }

            // Sipariş durumunu güncelle
            return $this->processPaymentResult($callbackData);

        } catch (\Exception $e) {
            Log::error('PayTR callback işleme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return PaymentCallbackResult::failure(
                orderNumber: $request->input('merchant_oid', 'unknown'),
                errorMessage: 'PayTR callback işleme hatası: ' . $e->getMessage(),
                errorCode: 'CALLBACK_PROCESSING_ERROR'
            );
        }
    }

    /**
     * PayTR callback verilerini request'den çıkarır
     */
    private function extractCallbackData(Request $request): array
    {
        return [
            'merchant_oid' => $request->input('merchant_oid'),
            'status' => $request->input('status'),
            'total_amount' => (int) $request->input('total_amount'), // Kuruş cinsinden
            'hash' => $request->input('hash'),
            'failed_reason_code' => $request->input('failed_reason_code'),
            'failed_reason_msg' => $request->input('failed_reason_msg'),
            'test_mode' => $request->input('test_mode', 0),
            'payment_type' => $request->input('payment_type'),
            'installment_count' => $request->input('installment_count', 1),
            'currency' => $request->input('currency', 'TL'),
        ];
    }

    /**
     * PayTR callback hash doğrulaması
     * PayTR'den gelen hash ile kendi oluşturduğumuz hash'i karşılaştırır
     */
    private function validateCallbackHash(array $callbackData): bool
    {
        $receivedHash = $callbackData['hash'];
        $merchantOid = $callbackData['merchant_oid'];
        $status = $callbackData['status'];
        $totalAmount = $callbackData['total_amount'];

        // PayTR callback hash formülü: merchant_oid + merchant_salt + status + total_amount
        $hashString = $merchantOid . 
                     $this->config['merchant_salt'] . 
                     $status . 
                     $totalAmount;

        // HMAC-SHA256 ile hash oluştur
        $expectedHash = base64_encode(hash_hmac('sha256', $hashString, $this->config['merchant_key'], true));

        $isValid = hash_equals($expectedHash, $receivedHash);

        Log::debug('PayTR hash doğrulama', [
            'merchant_oid' => $merchantOid,
            'status' => $status,
            'total_amount' => $totalAmount,
            'hash_valid' => $isValid,
            'expected_hash_length' => strlen($expectedHash),
            'received_hash_length' => strlen($receivedHash)
        ]);

        return $isValid;
    }

    /**
     * Ödeme sonucunu işler ve sipariş durumunu günceller
     */
    private function processPaymentResult(array $callbackData): PaymentCallbackResult
    {
        $merchantOid = $callbackData['merchant_oid'];
        $status = $callbackData['status'];
        $totalAmount = $callbackData['total_amount'] / 100; // Kuruştan TL'ye çevir
        
        // Siparişi bul
        $order = Order::where('order_number', $merchantOid)->first();
        
        if (!$order) {
            Log::error('PayTR callback: Sipariş bulunamadı', [
                'merchant_oid' => $merchantOid
            ]);
            
            return PaymentCallbackResult::failure(
                orderNumber: $merchantOid,
                errorMessage: 'Sipariş bulunamadı',
                errorCode: 'ORDER_NOT_FOUND'
            );
        }

        // Tutar doğrulama
        if (abs($order->total_amount - $totalAmount) > 0.01) {
            Log::error('PayTR callback: Tutar uyumsuzluğu', [
                'merchant_oid' => $merchantOid,
                'order_amount' => $order->total_amount,
                'callback_amount' => $totalAmount
            ]);
            
            return PaymentCallbackResult::failure(
                orderNumber: $merchantOid,
                errorMessage: 'Tutar uyumsuzluğu',
                errorCode: 'AMOUNT_MISMATCH'
            );
        }

        if ($status === 'success') {
            // Başarılı ödeme
            $this->updateOrderForSuccessfulPayment($order, $callbackData);
            
            Log::info('PayTR ödeme başarılı', [
                'order_id' => $order->id,
                'order_number' => $merchantOid,
                'amount' => $totalAmount,
                'installment_count' => $callbackData['installment_count']
            ]);

            return PaymentCallbackResult::success(
                orderNumber: $merchantOid,
                transactionId: $this->generateTransactionId($merchantOid),
                amount: $totalAmount,
                currency: $this->mapPayTrCurrency($callbackData['currency']),
                status: 'completed',
                metadata: [
                    'payment_provider' => 'paytr',
                    'installment_count' => $callbackData['installment_count'],
                    'payment_type' => $callbackData['payment_type'],
                    'test_mode' => (bool) $callbackData['test_mode']
                ]
            );
        } else {
            // Başarısız ödeme
            $this->updateOrderForFailedPayment($order, $callbackData);
            
            Log::warning('PayTR ödeme başarısız', [
                'order_id' => $order->id,
                'order_number' => $merchantOid,
                'failed_reason_code' => $callbackData['failed_reason_code'],
                'failed_reason_msg' => $callbackData['failed_reason_msg']
            ]);

            return PaymentCallbackResult::failure(
                orderNumber: $merchantOid,
                errorMessage: $callbackData['failed_reason_msg'] ?? 'Ödeme başarısız',
                errorCode: 'PAYMENT_FAILED_' . ($callbackData['failed_reason_code'] ?? 'UNKNOWN'),
                metadata: [
                    'payment_provider' => 'paytr',
                    'failed_reason_code' => $callbackData['failed_reason_code'],
                    'test_mode' => (bool) $callbackData['test_mode']
                ]
            );
        }
    }

    /**
     * Başarılı ödeme için sipariş durumunu günceller
     */
    private function updateOrderForSuccessfulPayment(Order $order, array $callbackData): void
    {
        $order->update([
            'payment_status' => 'paid',
            'payment_transaction_id' => $this->generateTransactionId($order->order_number),
            'status' => 'processing', // Ödeme başarılı, sipariş işleme alındı
            'payment_method' => 'paytr',
        ]);

        // Stok düşüm işlemi (eğer henüz yapılmamışsa)
        $this->reduceProductStock($order);
        
        // Başarılı ödeme bildirimi (email, SMS vb.)
        // event(new OrderPaymentSuccessful($order));
    }

    /**
     * Başarısız ödeme için sipariş durumunu günceller
     */
    private function updateOrderForFailedPayment(Order $order, array $callbackData): void
    {
        $order->update([
            'payment_status' => 'failed',
            'status' => 'cancelled', // Ödeme başarısız, sipariş iptal
            'notes' => ($order->notes ?? '') . "\nPayTR Ödeme Hatası: " . ($callbackData['failed_reason_msg'] ?? 'Bilinmeyen hata')
        ]);

        // Başarısız ödeme bildirimi
        // event(new OrderPaymentFailed($order));
    }

    /**
     * Ürün stoklarını düşürür
     */
    private function reduceProductStock(Order $order): void
    {
        foreach ($order->items as $item) {
            // Ana ürün stoğu
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
            
            // Varyant stoğu
            if ($item->productVariant) {
                $item->productVariant->decrement('stock', $item->quantity);
            }
        }

        Log::info('Ürün stokları düşürüldü', [
            'order_id' => $order->id,
            'items_count' => $order->items->count()
        ]);
    }

    /**
     * Transaction ID oluşturur
     */
    private function generateTransactionId(string $orderNumber): string
    {
        return 'PAYTR_' . $orderNumber . '_' . time();
    }

    /**
     * PayTR para birimini sistem formatına çevirir
     */
    private function mapPayTrCurrency(string $paytrCurrency): string
    {
        return match($paytrCurrency) {
            'TL' => 'TRY',
            'USD' => 'USD',
            'EUR' => 'EUR',
            default => 'TRY'
        };
    }
}