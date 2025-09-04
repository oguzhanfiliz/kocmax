<?php

declare(strict_types=1);

namespace App\Services\Payment\Strategies;

use App\Models\Order;
use App\ValueObjects\Payment\PaymentInitializationResult;
use App\ValueObjects\Payment\PaymentCallbackResult;
use App\ValueObjects\Payment\PaymentRefundResult;
use App\Exceptions\Payment\PaymentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * B2B kredi ödeme strategy'si
 * Mevcut OrderPaymentService'den uyarlandı
 */
class CreditPaymentStrategy extends AbstractPaymentStrategy
{
    public function __construct(bool $testMode = false)
    {
        parent::__construct($testMode);
        $this->supportedPaymentMethods = ['credit'];
        $this->supportedCurrencies = ['TRY', 'USD', 'EUR'];
    }

    public function getProviderName(): string
    {
        return 'credit';
    }

    public function initializePayment(Order $order, array $options = []): PaymentInitializationResult
    {
        try {
            // B2B kredi ödeme doğrulaması
            if (!$order->user || $order->customer_type !== 'B2B') {
                return PaymentInitializationResult::failure(
                    'Credit payment only available for B2B customers',
                    'INVALID_CUSTOMER_TYPE'
                );
            }

            $creditLimit = $order->user->credit_limit ?? 0;
            $currentCredit = $this->getCurrentCreditUsage($order->user);
            
            if (($currentCredit + $order->total_amount) > $creditLimit) {
                $availableCredit = $creditLimit - $currentCredit;
                return PaymentInitializationResult::failure(
                    "Insufficient credit limit. Available: {$availableCredit}, Required: {$order->total_amount}",
                    'INSUFFICIENT_CREDIT'
                );
            }

            // Kredi ödeme için token oluştur (basit approval token)
            $token = $this->generateTransactionId('CREDIT_APPROVAL');
            
            // Kredi kullanımını güncelle
            $this->updateCreditUsage($order->user, $order->total_amount);
            
            Log::info('Credit payment initialized', [
                'order_number' => $order->order_number,
                'credit_used' => $order->total_amount,
                'remaining_credit' => $creditLimit - ($currentCredit + $order->total_amount)
            ]);

            return PaymentInitializationResult::success(
                token: $token,
                metadata: [
                    'payment_method' => 'credit',
                    'credit_used' => $order->total_amount,
                    'remaining_credit' => $creditLimit - ($currentCredit + $order->total_amount),
                    'auto_approved' => true
                ]
            );

        } catch (\Exception $e) {
            Log::error('Credit payment initialization failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);

            return PaymentInitializationResult::failure(
                'Credit payment initialization failed: ' . $e->getMessage(),
                'CREDIT_INITIALIZATION_ERROR'
            );
        }
    }

    public function handleCallback(Request $request): PaymentCallbackResult
    {
        // Credit payments genellikle otomatik onaylanır, callback gerektirmez
        // Ama yine de webhook desteği için implement ediyoruz
        
        $orderNumber = $request->input('order_number') ?? $request->input('merchant_oid');
        $status = $request->input('status', 'success');
        $transactionId = $request->input('transaction_id') ?? $this->generateTransactionId('CREDIT');

        if ($status === 'success') {
            return PaymentCallbackResult::success(
                orderNumber: $orderNumber,
                transactionId: $transactionId,
                amount: (float) ($request->input('amount') ?? 0),
                currency: $request->input('currency', 'TRY'),
                status: 'completed'
            );
        }

        return PaymentCallbackResult::failure(
            orderNumber: $orderNumber,
            errorMessage: 'Credit payment callback failed',
            errorCode: 'CREDIT_CALLBACK_FAILED',
            transactionId: $transactionId
        );
    }

    public function processRefund(Order $order, float $amount, ?string $reason = null): PaymentRefundResult
    {
        try {
            // Kredi iadesi - kredi limitini geri ver
            $this->updateCreditUsage($order->user, -$amount);
            
            $refundTransactionId = $this->generateTransactionId('REFUND_CREDIT');
            
            Log::info('Credit refund processed', [
                'order_number' => $order->order_number,
                'refund_amount' => $amount,
                'reason' => $reason
            ]);

            return PaymentRefundResult::success(
                orderNumber: $order->order_number,
                refundAmount: $amount,
                currency: $order->currency_code ?? 'TRY',
                refundTransactionId: $refundTransactionId,
                originalTransactionId: $order->payment_transaction_id,
                reason: $reason,
                metadata: [
                    'refund_method' => 'credit',
                    'credit_restored' => $amount
                ]
            );

        } catch (\Exception $e) {
            Log::error('Credit refund failed', [
                'order_number' => $order->order_number,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return PaymentRefundResult::failure(
                orderNumber: $order->order_number,
                refundAmount: $amount,
                currency: $order->currency_code ?? 'TRY',
                errorMessage: 'Credit refund failed: ' . $e->getMessage(),
                errorCode: 'CREDIT_REFUND_ERROR'
            );
        }
    }

    public function validateConfiguration(): bool
    {
        // Kredi ödeme için özel konfigürasyon gerektirmez
        return true;
    }

    /**
     * Mevcut kredi kullanımını hesaplar
     */
    private function getCurrentCreditUsage($user): float
    {
        return (float) (Order::where('user_id', $user->id)
            ->where('customer_type', 'B2B')
            ->where('payment_method', 'credit')
            ->where('payment_status', '!=', 'paid')
            ->whereNotIn('status', ['cancelled', 'delivered'])
            ->sum('total_amount') ?? 0);
    }

    /**
     * Kredi kullanımını günceller
     */
    private function updateCreditUsage($user, float $amount): void
    {
        // Gerçek implementasyonda ayrı bir credit usage tablosu olabilir
        // Şimdilik sadece log'layalım
        Log::info('Credit usage updated', [
            'user_id' => $user->id,
            'amount_change' => $amount,
            'timestamp' => now()
        ]);
    }
}