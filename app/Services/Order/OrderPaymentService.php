<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\ValueObjects\Order\PaymentResult;
use App\Exceptions\Order\PaymentProcessingException;
use App\Exceptions\Order\InvalidPaymentMethodException;
use App\Exceptions\Order\InsufficientCreditException;
use Illuminate\Support\Facades\Log;

class OrderPaymentService
{
    private array $supportedMethods = ['card', 'credit', 'bank_transfer', 'wallet'];

    /**
     * Ödeme işlemini gerçekleştirir.
     *
     * @param Order $order Sipariş
     * @param array $paymentData Ödeme verileri
     * @return PaymentResult Ödeme sonucu
     */
    public function processPayment(Order $order, array $paymentData): PaymentResult
    {
        try {
            $paymentMethod = $paymentData['method'] ?? 'card';
            
            if (!in_array($paymentMethod, $this->supportedMethods)) {
                throw new InvalidPaymentMethodException("Unsupported payment method: {$paymentMethod}");
            }
            
            $result = match($paymentMethod) {
                'card' => $this->processCardPayment($order, $paymentData),
                'credit' => $this->processCreditPayment($order, $paymentData),
                'bank_transfer' => $this->processBankTransferPayment($order, $paymentData),
                'wallet' => $this->processWalletPayment($order, $paymentData),
                default => throw new InvalidPaymentMethodException("Unsupported payment method: {$paymentMethod}")
            };

            // Sipariş ödeme durumunu güncelle
            $this->updateOrderPaymentStatus($order, $result);
            
            Log::info('Payment processed', [
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'success' => $result->isSuccess(),
                'transaction_id' => $result->getTransactionId()
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'payment_data' => array_diff_key($paymentData, ['card_number', 'card_cvv']) // Hassas verileri loglama
            ]);
            
            return PaymentResult::failure($e->getMessage());
        }
    }

    /**
     * İade işlemini gerçekleştirir.
     *
     * @param Order $order Sipariş
     * @param float $refundAmount İade tutarı
     * @param string|null $reason İade nedeni
     * @return PaymentResult İade sonucu
     */
    public function processRefund(Order $order, float $refundAmount, ?string $reason = null): PaymentResult
    {
        try {
            if ($refundAmount <= 0 || $refundAmount > $order->total_amount) {
                throw new PaymentProcessingException("Invalid refund amount: {$refundAmount}");
            }

            $result = match($order->payment_method) {
                'credit' => $this->processCreditRefund($order, $refundAmount, $reason),
                'card' => $this->processCardRefund($order, $refundAmount, $reason),
                'bank_transfer' => $this->processBankTransferRefund($order, $refundAmount, $reason),
                'wallet' => $this->processWalletRefund($order, $refundAmount, $reason),
                default => throw new InvalidPaymentMethodException("Cannot refund payment method: {$order->payment_method}")
            };

            Log::info('Refund processed', [
                'order_id' => $order->id,
                'method' => $order->payment_method,
                'amount' => $refundAmount,
                'success' => $result->isSuccess(),
                'reason' => $reason
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'order_id' => $order->id,
                'amount' => $refundAmount,
                'error' => $e->getMessage()
            ]);
            
            return PaymentResult::failure($e->getMessage());
        }
    }

    /**
     * Kart ile ödemeyi işler (mock).
     *
     * @param Order $order Sipariş
     * @param array $paymentData Ödeme verileri
     * @return PaymentResult Sonuç
     */
    private function processCardPayment(Order $order, array $paymentData): PaymentResult
    {
        // Kart ödeme işleme (sahte/mock)
        // Gerçek uygulamada Iyzico, PayTR gibi ödeme sağlayıcıları ile entegre edilir
        
        $requiredFields = ['card_number', 'card_expiry', 'card_cvv', 'cardholder_name'];
        foreach ($requiredFields as $field) {
            if (empty($paymentData[$field])) {
                throw new PaymentProcessingException("Missing required field: {$field}");
            }
        }

        // Sahte ödeme geçidi çağrısı
        $isSuccess = $this->mockPaymentGatewayCall($order, $paymentData);
        
        if ($isSuccess) {
            $transactionId = 'TXN_' . $order->order_number . '_' . time();
            return PaymentResult::success($transactionId, [
                'payment_method' => 'card',
                'last_four' => substr($paymentData['card_number'], -4)
            ]);
        } else {
            return PaymentResult::failure('Card payment failed', 'DECLINE_CODE_001');
        }
    }

    /**
     * B2B kredi ile ödemeyi işler.
     *
     * @param Order $order Sipariş
     * @param array $paymentData Ödeme verileri
     * @return PaymentResult Sonuç
     */
    private function processCreditPayment(Order $order, array $paymentData): PaymentResult
    {
        // B2B kredi ödemesi doğrulaması
        if (!$order->user || $order->customer_type !== 'B2B') {
            throw new InvalidPaymentMethodException('Credit payment only available for B2B customers');
        }

        $creditLimit = $order->user->credit_limit ?? 0;
        $currentCredit = $this->getCurrentCreditUsage($order->user);
        
        if (($currentCredit + $order->total_amount) > $creditLimit) {
            $availableCredit = $creditLimit - $currentCredit;
            throw new InsufficientCreditException("Insufficient credit limit. Available: {$availableCredit}, Required: {$order->total_amount}");
        }

        // Kredi kullanımını güncelle
        $this->updateCreditUsage($order->user, $order->total_amount);
        
        $transactionId = "CREDIT_{$order->order_number}";
        return PaymentResult::success($transactionId, [
            'payment_method' => 'credit',
            'credit_used' => $order->total_amount,
            'remaining_credit' => $creditLimit - ($currentCredit + $order->total_amount)
        ]);
    }

    /**
     * Havale/EFT ödemeyi işler.
     *
     * @param Order $order Sipariş
     * @param array $paymentData Ödeme verileri
     * @return PaymentResult Sonuç
     */
    private function processBankTransferPayment(Order $order, array $paymentData): PaymentResult
    {
        // Banka transferi manuel doğrulama gerektirir
        $transactionId = "TRANSFER_{$order->order_number}";
        
        return PaymentResult::success($transactionId, [
            'payment_method' => 'bank_transfer',
            'status' => 'pending_verification',
            'bank_reference' => $paymentData['bank_reference'] ?? null
        ]);
    }

    /**
     * Dijital cüzdan ödemesini işler.
     *
     * @param Order $order Sipariş
     * @param array $paymentData Ödeme verileri
     * @return PaymentResult Sonuç
     */
    private function processWalletPayment(Order $order, array $paymentData): PaymentResult
    {
        // Dijital cüzdan ödemesi (PayPal, Apple Pay vb.)
        $walletType = $paymentData['wallet_type'] ?? 'unknown';
        $transactionId = "WALLET_{$order->order_number}_" . time();
        
        return PaymentResult::success($transactionId, [
            'payment_method' => 'wallet',
            'wallet_type' => $walletType
        ]);
    }

    /**
     * Kredi ile yapılan ödemede iade işlemini gerçekleştirir.
     *
     * @param Order $order Sipariş
     * @param float $refundAmount Tutar
     * @param string|null $reason Neden
     * @return PaymentResult Sonuç
     */
    private function processCreditRefund(Order $order, float $refundAmount, ?string $reason): PaymentResult
    {
        // Kredi iadesi — kredi limitini eski haline getir
        $this->updateCreditUsage($order->user, -$refundAmount);
        
        $transactionId = "REFUND_CREDIT_{$order->order_number}";
        return PaymentResult::success($transactionId, [
            'refund_method' => 'credit',
            'refund_amount' => $refundAmount,
            'reason' => $reason
        ]);
    }

    /**
     * Kartla yapılan ödeme iadesini işler (mock).
     *
     * @param Order $order Sipariş
     * @param float $refundAmount Tutar
     * @param string|null $reason Neden
     * @return PaymentResult Sonuç
     */
    private function processCardRefund(Order $order, float $refundAmount, ?string $reason): PaymentResult
    {
        // Ödeme geçidi üzerinden sahte kart iadesi
        $transactionId = "REFUND_CARD_{$order->order_number}_" . time();
        
        return PaymentResult::success($transactionId, [
            'refund_method' => 'card',
            'refund_amount' => $refundAmount,
            'original_transaction' => $order->payment_transaction_id,
            'reason' => $reason
        ]);
    }

    /**
     * Havale/EFT iadesini işler.
     *
     * @param Order $order Sipariş
     * @param float $refundAmount Tutar
     * @param string|null $reason Neden
     * @return PaymentResult Sonuç
     */
    private function processBankTransferRefund(Order $order, float $refundAmount, ?string $reason): PaymentResult
    {
        $transactionId = "REFUND_TRANSFER_{$order->order_number}";
        
        return PaymentResult::success($transactionId, [
            'refund_method' => 'bank_transfer',
            'refund_amount' => $refundAmount,
            'status' => 'pending_manual_transfer',
            'reason' => $reason
        ]);
    }

    /**
     * Dijital cüzdan iadesini işler.
     *
     * @param Order $order Sipariş
     * @param float $refundAmount Tutar
     * @param string|null $reason Neden
     * @return PaymentResult Sonuç
     */
    private function processWalletRefund(Order $order, float $refundAmount, ?string $reason): PaymentResult
    {
        $transactionId = "REFUND_WALLET_{$order->order_number}_" . time();
        
        return PaymentResult::success($transactionId, [
            'refund_method' => 'wallet',
            'refund_amount' => $refundAmount,
            'reason' => $reason
        ]);
    }

    /**
     * Ödeme sonucuna göre siparişin ödeme durumunu günceller.
     *
     * @param Order $order Sipariş
     * @param PaymentResult $result Sonuç
     * @return void
     */
    private function updateOrderPaymentStatus(Order $order, PaymentResult $result): void
    {
        if ($result->isSuccess()) {
            $order->update([
                'payment_status' => 'paid',
                'payment_transaction_id' => $result->getTransactionId()
            ]);
        } else {
            $order->update([
                'payment_status' => 'failed'
            ]);
        }
    }

    /**
     * Sahte ödeme geçidi çağrısı yapar (başarı oranı %90).
     *
     * @param Order $order Sipariş
     * @param array $paymentData Ödeme verileri
     * @return bool Başarılı mı
     */
    private function mockPaymentGatewayCall(Order $order, array $paymentData): bool
    {
        // Başarı oranı: %90
        return mt_rand(1, 100) <= 90;
    }

    /**
     * Kullanıcının mevcut kredi kullanımını hesaplar.
     *
     * @param mixed $user Kullanıcı modeli
     * @return float Toplam kullanım
     */
    private function getCurrentCreditUsage($user): float
    {
        // Ödenmemiş B2B siparişlerden mevcut kredi kullanımını hesapla
        return (float) (Order::where('user_id', $user->id)
            ->where('customer_type', 'B2B')
            ->where('payment_method', 'credit')
            ->where('payment_status', '!=', 'paid')
            ->whereNotIn('status', ['cancelled', 'delivered'])
            ->sum('total_amount') ?? 0);
    }

    /**
     * Kredi kullanımını günceller (gerçek uygulamada ayrı bir tablo güncellenebilir).
     *
     * @param mixed $user Kullanıcı modeli
     * @param float $amount Değişim tutarı
     * @return void
     */
    private function updateCreditUsage($user, float $amount): void
    {
        // Gerçek uygulamada ayrı bir kredi kullanım tablosu güncellenebilir
        // Şimdilik yalnızca kredi kullanım değişikliğini loglayalım
        Log::info('Credit usage updated', [
            'user_id' => $user->id,
            'amount_change' => $amount,
            'timestamp' => now()
        ]);
    }
}