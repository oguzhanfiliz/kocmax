<?php

declare(strict_types=1);

namespace App\Services\Payment\Strategies;

use App\Models\Order;
use App\ValueObjects\Payment\PaymentInitializationResult;
use App\ValueObjects\Payment\PaymentCallbackResult;
use App\ValueObjects\Payment\PaymentRefundResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Kredi kartı ödeme strategy'si
 * Generic kart ödeme implementasyonu (PayTR, Iyzico gibi özel implementasyonlar ayrı)
 */
class CardPaymentStrategy extends AbstractPaymentStrategy
{
    public function __construct(bool $testMode = false)
    {
        parent::__construct($testMode);
        $this->supportedPaymentMethods = ['card', 'credit_card', 'debit_card'];
        $this->supportedCurrencies = ['TRY', 'USD', 'EUR'];
    }

    public function getProviderName(): string
    {
        return 'card';
    }

    public function initializePayment(Order $order, array $options = []): PaymentInitializationResult
    {
        try {
            // Kart ödeme için gerekli alanları kontrol et
            $requiredFields = ['card_number', 'card_expiry', 'card_cvv', 'cardholder_name'];
            $this->validateRequiredFields($options, $requiredFields);

            // Para birimi doğrulaması
            $this->validateCurrency($order->currency_code ?? 'TRY');

            // Mock payment gateway çağrısı (gerçek implementasyonda external API)
            $isSuccess = $this->mockPaymentGatewayCall([
                'amount' => $order->total_amount,
                'currency' => $order->currency_code ?? 'TRY',
                'card_last_four' => substr($options['card_number'], -4),
                'order_number' => $order->order_number
            ]);

            if ($isSuccess) {
                $transactionId = $this->generateTransactionId('CARD');
                
                Log::info('Card payment initialized successfully', [
                    'order_number' => $order->order_number,
                    'transaction_id' => $transactionId,
                    'card_last_four' => substr($options['card_number'], -4),
                    'amount' => $order->total_amount
                ]);

                return PaymentInitializationResult::success(
                    token: $transactionId,
                    metadata: [
                        'payment_method' => 'card',
                        'card_last_four' => substr($options['card_number'], -4),
                        'cardholder_name' => $options['cardholder_name'],
                        'transaction_id' => $transactionId,
                        'requires_3ds' => $this->requires3DS($options),
                        'processing_time_estimate' => '1-2 minutes'
                    ]
                );
            } else {
                return PaymentInitializationResult::failure(
                    'Card payment was declined by the bank',
                    'CARD_DECLINED'
                );
            }

        } catch (\InvalidArgumentException $e) {
            return PaymentInitializationResult::failure(
                $e->getMessage(),
                'VALIDATION_ERROR'
            );
        } catch (\Exception $e) {
            Log::error('Card payment initialization failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);

            return PaymentInitializationResult::failure(
                'Card payment initialization failed: ' . $e->getMessage(),
                'CARD_INITIALIZATION_ERROR'
            );
        }
    }

    public function handleCallback(Request $request): PaymentCallbackResult
    {
        $orderNumber = $request->input('order_number') ?? $request->input('orderNumber');
        $status = $request->input('status', 'pending');
        $transactionId = $request->input('transaction_id') ?? $request->input('transactionId');
        $amount = (float) ($request->input('amount') ?? 0);

        if (in_array($status, ['success', 'completed', 'approved'])) {
            return PaymentCallbackResult::success(
                orderNumber: $orderNumber,
                transactionId: $transactionId,
                amount: $amount,
                currency: $request->input('currency', 'TRY'),
                status: 'completed',
                metadata: [
                    'payment_method' => 'card',
                    'bank_response_code' => $request->input('bank_response_code'),
                    'bank_response_message' => $request->input('bank_response_message'),
                    'installment' => $request->input('installment', 1),
                    '3ds_verified' => $request->input('3ds_verified', false)
                ]
            );
        }

        return PaymentCallbackResult::failure(
            orderNumber: $orderNumber,
            errorMessage: $request->input('error_message', 'Card payment failed'),
            errorCode: $request->input('error_code', 'CARD_PAYMENT_FAILED'),
            transactionId: $transactionId,
            metadata: [
                'status' => $status,
                'bank_response_code' => $request->input('bank_response_code'),
                'bank_response_message' => $request->input('bank_response_message')
            ]
        );
    }

    public function processRefund(Order $order, float $amount, ?string $reason = null): PaymentRefundResult
    {
        try {
            // Mock refund işlemi - gerçek implementasyonda payment gateway API'si
            $refundSuccess = $this->mockPaymentGatewayCall([
                'operation' => 'refund',
                'original_transaction_id' => $order->payment_transaction_id,
                'refund_amount' => $amount
            ]);

            if ($refundSuccess) {
                $refundTransactionId = $this->generateTransactionId('REFUND_CARD');
                
                Log::info('Card refund processed successfully', [
                    'order_number' => $order->order_number,
                    'refund_transaction_id' => $refundTransactionId,
                    'refund_amount' => $amount,
                    'original_transaction' => $order->payment_transaction_id
                ]);

                return PaymentRefundResult::success(
                    orderNumber: $order->order_number,
                    refundAmount: $amount,
                    currency: $order->currency_code ?? 'TRY',
                    refundTransactionId: $refundTransactionId,
                    originalTransactionId: $order->payment_transaction_id,
                    reason: $reason,
                    metadata: [
                        'refund_method' => 'card',
                        'estimated_refund_time' => '3-5 business days',
                        'refund_to_original_card' => true
                    ]
                );
            } else {
                return PaymentRefundResult::failure(
                    orderNumber: $order->order_number,
                    refundAmount: $amount,
                    currency: $order->currency_code ?? 'TRY',
                    errorMessage: 'Card refund was declined by the bank',
                    errorCode: 'REFUND_DECLINED',
                    originalTransactionId: $order->payment_transaction_id
                );
            }

        } catch (\Exception $e) {
            Log::error('Card refund failed', [
                'order_number' => $order->order_number,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return PaymentRefundResult::failure(
                orderNumber: $order->order_number,
                refundAmount: $amount,
                currency: $order->currency_code ?? 'TRY',
                errorMessage: 'Card refund processing failed: ' . $e->getMessage(),
                errorCode: 'CARD_REFUND_ERROR'
            );
        }
    }

    public function validateConfiguration(): bool
    {
        // Generic kart ödemesi için özel konfigürasyon gerektirmez
        return true;
    }

    /**
     * 3D Secure gerekip gerekmediğini kontrol eder
     */
    private function requires3DS(array $cardData): bool
    {
        // Basit kural: büyük tutarlar veya yabancı kartlar için 3DS gerekli
        // Gerçek implementasyonda daha karmaşık kurallar olabilir
        $amount = (float) ($cardData['amount'] ?? 0);
        $cardBin = substr($cardData['card_number'], 0, 6);
        
        // 500 TL üzeri için 3DS gerekli (örnek kural)
        if ($amount > 500) {
            return true;
        }
        
        // Yabancı kart BIN'leri için 3DS (basit kontrol)
        $domesticBins = ['4546', '5274', '4355']; // Örnek Türk banka BIN'leri
        if (!in_array(substr($cardBin, 0, 4), $domesticBins)) {
            return true;
        }
        
        return false;
    }
}