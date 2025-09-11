<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\ValueObjects\Payment\PaymentCallbackResult;
use App\Exceptions\Payment\PaymentException;
use App\Services\Order\OrderStockService;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PayTR callback işleme servisi
 * PayTR'den gelen ödeme sonuç bildirimlerini güvenli şekilde işler
 */
class PayTrCallbackHandler
{
    private array $config;

    public function __construct(
        private OrderStockService $stockService,
        private OrderService $orderService
    ) {
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
                transactionId: $merchantOid, // PayTr'de transaction ID yok, merchant_oid kullan
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
     * Transaction güvenliği ile sipariş durumunu günceller ve stokları düşürür
     */
    private function updateOrderForSuccessfulPayment(Order $order, array $callbackData): void
    {
        DB::transaction(function () use ($order, $callbackData) {
            // Sipariş durumunu güncelle (Order model method kullan)
            // PayTr'de transaction ID yok, sadece merchant_oid var
            $order->update([
                'payment_status' => 'paid',
                'payment_transaction_id' => $order->order_number, // merchant_oid'i transaction ID olarak kullan
                'paid_at' => now(),
                'payment_method' => 'paytr',
                'notes' => ($order->notes ?? '') . "\n[PayTR] Ödeme başarılı: " . now()->format('d.m.Y H:i')
            ]);

            // Durumu servis üzerinden güncelle (geçmiş + event)
            $this->orderService->updateStatus($order, OrderStatus::Processing, null, 'PayTR payment confirmed');

            // Stok düşüm işlemi (güvenli ve atomik)
            try {
                $this->stockService->reduceOrderStock($order);
                
                Log::info('PayTR ödeme sonrası stok işlemleri tamamlandı', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
            } catch (\Exception $e) {
                Log::error('PayTR ödeme sonrası stok düşürme hatası', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                
                // Stok hatası durumunda siparişi beklemeye al
                $order->update([
                    'status' => 'pending',
                    'notes' => ($order->notes ?? '') . "\n[SYS] Stok hatası - Manuel kontrol gerekli"
                ]);
                
                throw $e; // Transaction rollback tetikler
            }

            // Başarılı ödeme bildirimi (email, SMS vb.)
            // event(new OrderPaymentSuccessful($order));
        });
    }

    /**
     * Başarısız ödeme için sipariş durumunu günceller
     * Transaction güvenliği ile sipariş iptal edilir ve stoklar geri yüklenir
     */
    private function updateOrderForFailedPayment(Order $order, array $callbackData): void
    {
        DB::transaction(function () use ($order, $callbackData) {
            // Ödeme durumunu güncelle (status hariç)
            $order->update([
                'payment_status' => 'failed',
                'cancelled_at' => now(),
                'notes' => ($order->notes ?? '') . "\n[PayTR] Ödeme hatası: " . ($callbackData['failed_reason_msg'] ?? 'Bilinmeyen hata') . ' - ' . now()->format('d.m.Y H:i')
            ]);

            // Durumu servis üzerinden iptal et
            $this->orderService->updateStatus($order, OrderStatus::Cancelled, null, 'PayTR payment failed');

            // Eğer daha önce stok düşürüldüyse geri yükle
            if ($order->payment_status === 'paid' || str_contains($order->notes ?? '', 'Stoklar düşürüldü')) {
                try {
                    $this->stockService->restoreOrderStock($order);
                    
                    Log::info('PayTR ödeme hatası sonrası stoklar geri yüklendi', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ]);
                } catch (\Exception $e) {
                    Log::error('PayTR ödeme hatası sonrası stok geri yükleme hatası', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Stok geri yükleme hatası kritik değil, işleme devam et
                    $order->update([
                        'notes' => ($order->notes ?? '') . "\n[SYS] Stok geri yükleme hatası - Manuel kontrol gerekli"
                    ]);
                }
            }

            // Başarısız ödeme bildirimi
            // event(new OrderPaymentFailed($order));
        });
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
