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
 * Banka havalesi ödeme strategy'si
 * Manuel doğrulama gerektiren ödeme yöntemi
 */
class BankTransferPaymentStrategy extends AbstractPaymentStrategy
{
    public function __construct(bool $testMode = false)
    {
        parent::__construct($testMode);
        $this->supportedPaymentMethods = ['bank_transfer'];
        $this->supportedCurrencies = ['TRY'];
    }

    public function getProviderName(): string
    {
        return 'bank_transfer';
    }

    public function initializePayment(Order $order, array $options = []): PaymentInitializationResult
    {
        try {
            // Banka havalesi için reference number oluştur
            $referenceNumber = $this->generateBankReference($order);
            
            // Banka bilgilerini config'den al
            $bankInfo = $this->getBankAccountInfo();
            
            Log::info('Bank transfer payment initialized', [
                'order_number' => $order->order_number,
                'reference_number' => $referenceNumber,
                'amount' => $order->total_amount
            ]);

            return PaymentInitializationResult::success(
                token: $referenceNumber,
                metadata: [
                    'payment_method' => 'bank_transfer',
                    'status' => 'pending_transfer',
                    'reference_number' => $referenceNumber,
                    'bank_info' => $bankInfo,
                    'instructions' => $this->getTransferInstructions($order, $referenceNumber),
                    'requires_manual_verification' => true
                ]
            );

        } catch (\Exception $e) {
            Log::error('Bank transfer payment initialization failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);

            return PaymentInitializationResult::failure(
                'Bank transfer payment initialization failed: ' . $e->getMessage(),
                'BANK_TRANSFER_INITIALIZATION_ERROR'
            );
        }
    }

    public function handleCallback(Request $request): PaymentCallbackResult
    {
        // Banka havalesi callback'i genellikle manuel sistem yöneticisi tarafından tetiklenir
        // Veya otomatik banka API entegrasyonu ile gelebilir
        
        $orderNumber = $request->input('order_number') ?? $request->input('reference');
        $status = $request->input('status', 'pending');
        $bankReference = $request->input('bank_reference');
        $verificationCode = $request->input('verification_code');
        
        // Basit doğrulama - gerçek implementasyonda bank API ile doğrulama yapılabilir
        if ($status === 'verified' && !empty($bankReference)) {
            $transactionId = $this->generateTransactionId('TRANSFER');
            
            return PaymentCallbackResult::success(
                orderNumber: $orderNumber,
                transactionId: $transactionId,
                amount: (float) ($request->input('amount') ?? 0),
                currency: $request->input('currency', 'TRY'),
                status: 'completed',
                metadata: [
                    'bank_reference' => $bankReference,
                    'verification_code' => $verificationCode,
                    'verified_at' => now()->toISOString()
                ]
            );
        }

        return PaymentCallbackResult::failure(
            orderNumber: $orderNumber,
            errorMessage: 'Bank transfer verification failed or pending',
            errorCode: 'BANK_TRANSFER_PENDING',
            metadata: [
                'bank_reference' => $bankReference,
                'status' => $status
            ]
        );
    }

    public function processRefund(Order $order, float $amount, ?string $reason = null): PaymentRefundResult
    {
        try {
            // Banka havalesi iadesi manuel işlem gerektirir
            $refundTransactionId = $this->generateTransactionId('REFUND_TRANSFER');
            
            Log::info('Bank transfer refund initiated', [
                'order_number' => $order->order_number,
                'refund_amount' => $amount,
                'reason' => $reason,
                'status' => 'pending_manual_transfer'
            ]);

            return PaymentRefundResult::success(
                orderNumber: $order->order_number,
                refundAmount: $amount,
                currency: $order->currency_code ?? 'TRY',
                refundTransactionId: $refundTransactionId,
                originalTransactionId: $order->payment_transaction_id,
                reason: $reason,
                metadata: [
                    'refund_method' => 'bank_transfer',
                    'status' => 'pending_manual_transfer',
                    'requires_manual_processing' => true,
                    'customer_bank_info_required' => true
                ]
            );

        } catch (\Exception $e) {
            Log::error('Bank transfer refund failed', [
                'order_number' => $order->order_number,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return PaymentRefundResult::failure(
                orderNumber: $order->order_number,
                refundAmount: $amount,
                currency: $order->currency_code ?? 'TRY',
                errorMessage: 'Bank transfer refund initiation failed: ' . $e->getMessage(),
                errorCode: 'BANK_TRANSFER_REFUND_ERROR'
            );
        }
    }

    public function validateConfiguration(): bool
    {
        // Banka hesap bilgilerinin config'de tanımlanmış olduğunu kontrol et
        $bankInfo = config('payments.bank_transfer.account_info');
        return !empty($bankInfo['account_number']) && !empty($bankInfo['bank_name']);
    }

    /**
     * Banka referans numarası oluşturur
     */
    private function generateBankReference(Order $order): string
    {
        $prefix = 'BT';
        $orderNumber = str_replace(['ORD-', '-'], '', $order->order_number);
        $checksum = substr(md5($order->id . $order->total_amount), 0, 4);
        
        return "{$prefix}{$orderNumber}{$checksum}";
    }

    /**
     * Banka hesap bilgilerini getirir
     */
    private function getBankAccountInfo(): array
    {
        return config('payments.bank_transfer.account_info', [
            'bank_name' => 'Örnek Banka A.Ş.',
            'account_name' => 'ŞİRKET ADI',
            'account_number' => '1234567890',
            'iban' => 'TR123456789012345678901234',
            'swift_code' => 'EXAMPLE1'
        ]);
    }

    /**
     * Havale talimatlarını getirir
     */
    private function getTransferInstructions(Order $order, string $referenceNumber): array
    {
        return [
            'amount' => number_format($order->total_amount, 2) . ' ' . ($order->currency_code ?? 'TRY'),
            'reference' => $referenceNumber,
            'description' => "Sipariş No: {$order->order_number} - Ref: {$referenceNumber}",
            'instructions' => [
                '1. Yukarıdaki banka hesabına belirtilen tutarı havale edin',
                '2. Havale açıklaması kısmına referans numarasını yazın: ' . $referenceNumber,
                '3. Havale dekontu fotoğrafını müşteri hizmetlerine gönderin',
                '4. Havale onaylandıktan sonra siparişiniz hazırlanacaktır'
            ],
            'warning' => 'Referans numarası olmadan yapılan havaleler işleme alınmayacaktır.'
        ];
    }
}