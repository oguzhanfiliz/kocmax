<?php

declare(strict_types=1);

namespace App\Services\Payment\Strategies;

use App\Models\Order;
use App\ValueObjects\Payment\PaymentInitializationResult;
use App\ValueObjects\Payment\PaymentCallbackResult;
use App\ValueObjects\Payment\PaymentRefundResult;
use App\Services\Payment\PayTrTokenService;
use App\Services\Payment\PayTrCallbackHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PayTR iframe ödeme strategy'si
 * PayTR API dokümantasyonuna göre kredi kartı ödemelerini işler
 */
class PayTrPaymentStrategy extends AbstractPaymentStrategy
{
    private PayTrTokenService $tokenService;
    private PayTrCallbackHandler $callbackHandler;
    private array $config;

    public function __construct(bool $testMode = false)
    {
        parent::__construct($testMode);
        $this->supportedPaymentMethods = ['card', 'credit_card'];
        $this->supportedCurrencies = ['TRY', 'USD', 'EUR'];
        
        $this->tokenService = app(PayTrTokenService::class);
        $this->callbackHandler = app(PayTrCallbackHandler::class);
        $this->config = config('payments.providers.paytr', []);
    }

    public function getProviderName(): string
    {
        return 'paytr';
    }

    /**
     * PayTR iframe ödeme sürecini başlatır
     * Token oluşturur ve iframe URL'i döner
     * 
     * @param Order $order Ödeme yapılacak sipariş
     * @param array $options PayTR özel seçenekleri (installment, non_3d vb.)
     * @return PaymentInitializationResult PayTR token ve iframe bilgileri
     */
    public function initializePayment(Order $order, array $options = []): PaymentInitializationResult
    {
        try {
            Log::info('PayTR ödeme süreci başlatılıyor', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'test_mode' => $this->testMode,
                'options' => $options
            ]);

            // Para birimi kontrolü
            $this->validateCurrency($order->currency_code ?? 'TRY');

            // Sipariş bilgileri kontrolü
            $this->validateOrderRequirements($order);

            // PayTR token oluştur
            $paytrToken = $this->tokenService->generateToken($order, $options);

            // Token süre kontrolü
            if ($paytrToken->isExpired()) {
                throw new \Exception('PayTR token süresi dolmuş');
            }

            Log::info('PayTR token başarıyla oluşturuldu', [
                'order_number' => $order->order_number,
                'iframe_url' => $paytrToken->getIframeUrl(),
                'expires_at' => $paytrToken->getExpiresAt()->format('Y-m-d H:i:s'),
                'time_to_expiry' => $paytrToken->getTimeToExpiry() . ' saniye'
            ]);

            return PaymentInitializationResult::success(
                token: $paytrToken->getToken(),
                iframeUrl: $paytrToken->getIframeUrl(),
                metadata: [
                    'payment_provider' => 'paytr',
                    'iframe_url' => $paytrToken->getIframeUrl(),
                    'expires_at' => $paytrToken->getExpiresAt()->format('Y-m-d H:i:s'),
                    'time_to_expiry_seconds' => $paytrToken->getTimeToExpiry(),
                    'test_mode' => $this->testMode,
                    'max_installment' => $options['max_installment'] ?? $this->config['max_installment'] ?? 0,
                    'basket_data' => $paytrToken->getBasketData(),
                    'currency' => $order->currency_code ?? 'TRY',
                    'requires_iframe' => true
                ],
                expiresAt: $paytrToken->getExpiresAt()
            );

        } catch (\Exception $e) {
            Log::error('PayTR ödeme başlatma hatası', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return PaymentInitializationResult::failure(
                'PayTR ödeme başlatılamadı: ' . $e->getMessage(),
                'PAYTR_INITIALIZATION_ERROR',
                ['original_error' => $e->getMessage()]
            );
        }
    }

    /**
     * PayTR'den gelen callback'i işler
     * Hash doğrulaması yapar ve ödeme sonucunu döner
     * 
     * @param Request $request PayTR callback request'i
     * @return PaymentCallbackResult Callback işleme sonucu
     */
    public function handleCallback(Request $request): PaymentCallbackResult
    {
        Log::info('PayTR callback alındı', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'merchant_oid' => $request->input('merchant_oid'),
            'status' => $request->input('status')
        ]);

        // PayTR callback handler'ına yönlendir
        return $this->callbackHandler->handle($request);
    }

    /**
     * PayTR üzerinden iade işlemi
     * Not: PayTR API'si genellikle manuel iade gerektir
     */
    public function processRefund(Order $order, float $amount, ?string $reason = null): PaymentRefundResult
    {
        try {
            Log::info('PayTR iade işlemi başlatılıyor', [
                'order_number' => $order->order_number,
                'refund_amount' => $amount,
                'reason' => $reason
            ]);

            // PayTR genellikle manuel iade gerektirir
            // Bu işlem PayTR admin panelinden yapılmalı
            
            $refundTransactionId = $this->generateTransactionId('PAYTR_REFUND');
            
            Log::warning('PayTR iade manuel işlem gerektirir', [
                'order_number' => $order->order_number,
                'refund_transaction_id' => $refundTransactionId,
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
                    'refund_method' => 'paytr',
                    'requires_manual_processing' => true,
                    'admin_panel_url' => 'https://www.paytr.com/magaza/admin/',
                    'instructions' => 'PayTR admin panelinden manuel iade işlemi yapılmalıdır.',
                    'estimated_refund_time' => '1-3 iş günü (manuel işlem sonrası)'
                ]
            );

        } catch (\Exception $e) {
            Log::error('PayTR iade işlemi hatası', [
                'order_number' => $order->order_number,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return PaymentRefundResult::failure(
                orderNumber: $order->order_number,
                refundAmount: $amount,
                currency: $order->currency_code ?? 'TRY',
                errorMessage: 'PayTR iade işlemi başlatılamadı: ' . $e->getMessage(),
                errorCode: 'PAYTR_REFUND_ERROR',
                originalTransactionId: $order->payment_transaction_id
            );
        }
    }

    /**
     * PayTR konfigürasyonunu doğrula
     */
    public function validateConfiguration(): bool
    {
        $requiredFields = ['merchant_id', 'merchant_key', 'merchant_salt'];
        
        foreach ($requiredFields as $field) {
            if (empty($this->config[$field])) {
                Log::error('PayTR konfigürasyon eksik', ['missing_field' => $field]);
                return false;
            }
        }

        // URL'lerin geçerliliğini kontrol et
        $urls = ['callback_url', 'success_url', 'failure_url'];
        foreach ($urls as $urlField) {
            if (!empty($this->config[$urlField]) && !filter_var($this->config[$urlField], FILTER_VALIDATE_URL)) {
                Log::error('PayTR geçersiz URL', ['invalid_url_field' => $urlField]);
                return false;
            }
        }

        Log::info('PayTR konfigürasyon doğrulaması başarılı', [
            'merchant_id' => $this->config['merchant_id'],
            'test_mode' => $this->config['test_mode'] ?? false
        ]);

        return true;
    }

    /**
     * Sipariş gereksinimlerini doğrula
     */
    private function validateOrderRequirements(Order $order): void
    {
        // Email adresi zorunlu
        if (empty($order->billing_email)) {
            throw new \InvalidArgumentException('PayTR için müşteri email adresi gerekli');
        }

        // Sipariş tutarı kontrol
        if ($order->total_amount <= 0) {
            throw new \InvalidArgumentException('PayTR için geçersiz sipariş tutarı');
        }

        // Sipariş kalemleri kontrol
        if ($order->items->count() === 0) {
            throw new \InvalidArgumentException('PayTR için sipariş kalemleri gerekli');
        }

        // Müşteri bilgileri kontrol
        if (empty($order->billing_name) && empty($order->user?->name)) {
            throw new \InvalidArgumentException('PayTR için müşteri adı gerekli');
        }

        Log::debug('PayTR sipariş gereksinimleri doğrulandı', [
            'order_number' => $order->order_number,
            'items_count' => $order->items->count(),
            'has_email' => !empty($order->billing_email),
            'has_name' => !empty($order->billing_name) || !empty($order->user?->name)
        ]);
    }

    /**
     * PayTR için iade desteği (manuel)
     */
    public function canRefund(): bool
    {
        return true; // Manuel iade desteklenir
    }
}