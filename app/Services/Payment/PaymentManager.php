<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\Payment\PaymentProviderInterface;
use App\Models\Order;
use App\ValueObjects\Payment\PaymentInitializationResult;
use App\ValueObjects\Payment\PaymentCallbackResult;
use App\ValueObjects\Payment\PaymentRefundResult;
use App\Exceptions\Payment\PaymentProviderNotFoundException;
use App\Exceptions\Payment\PaymentConfigurationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * Ödeme sağlayıcıları yöneticisi
 * Strategy Pattern'in context sınıfıdır
 */
class PaymentManager
{
    /** @var Collection<PaymentProviderInterface> */
    private Collection $providers;

    public function __construct()
    {
        $this->providers = collect();
    }

    /**
     * Yeni ödeme sağlayıcısı kaydeder
     */
    public function register(string $name, PaymentProviderInterface $provider): self
    {
        // Konfigürasyon doğrulaması
        if (!$provider->validateConfiguration()) {
            Log::error('Payment provider configuration is invalid', [
                'provider' => $name,
                'provider_class' => get_class($provider)
            ]);
            
            throw new PaymentConfigurationException(
                "Payment provider '{$name}' has invalid configuration"
            );
        }

        $this->providers->put($name, $provider);

        Log::info('Payment provider registered', [
            'provider' => $name,
            'provider_class' => get_class($provider),
            'test_mode' => $provider->isTestMode(),
            'supported_currencies' => $provider->getSupportedCurrencies(),
            'supported_methods' => $provider->getSupportedPaymentMethods()
        ]);

        return $this;
    }

    /**
     * Ödeme sağlayıcısını kaldırır
     */
    public function unregister(string $name): self
    {
        $this->providers->forget($name);
        
        Log::info('Payment provider unregistered', ['provider' => $name]);
        
        return $this;
    }

    /**
     * Belirtilen ödeme sağlayıcısını getirir
     */
    public function getProvider(string $name): PaymentProviderInterface
    {
        if (!$this->providers->has($name)) {
            throw new PaymentProviderNotFoundException("Payment provider '{$name}' not found");
        }

        return $this->providers->get($name);
    }

    /**
     * Tüm kayıtlı ödeme sağlayıcılarını getirir
     */
    public function getAllProviders(): Collection
    {
        return $this->providers;
    }

    /**
     * Kayıtlı ödeme sağlayıcı isimlerini getirir
     */
    public function getProviderNames(): array
    {
        return $this->providers->keys()->toArray();
    }

    /**
     * Belirtilen sağlayıcının kayıtlı olup olmadığını kontrol eder
     */
    public function hasProvider(string $name): bool
    {
        return $this->providers->has($name);
    }

    /**
     * Ödeme sürecini başlatır
     */
    public function initializePayment(string $providerName, Order $order, array $options = []): PaymentInitializationResult
    {
        try {
            $provider = $this->getProvider($providerName);
            
            Log::info('Payment initialization started', [
                'provider' => $providerName,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total_amount,
                'currency' => $order->currency_code,
                'user_id' => $order->user_id,
                'test_mode' => $provider->isTestMode()
            ]);

            $result = $provider->initializePayment($order, $options);

            if ($result->isSuccess()) {
                Log::info('Payment initialization successful', [
                    'provider' => $providerName,
                    'order_number' => $order->order_number,
                    'has_token' => $result->hasToken(),
                    'has_iframe_url' => $result->hasIframeUrl(),
                    'has_redirect_url' => $result->hasRedirectUrl(),
                    'expires_at' => $result->getExpiresAt()?->format('Y-m-d H:i:s')
                ]);
            } else {
                Log::warning('Payment initialization failed', [
                    'provider' => $providerName,
                    'order_number' => $order->order_number,
                    'error_message' => $result->getErrorMessage(),
                    'error_code' => $result->getErrorCode()
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Payment initialization error', [
                'provider' => $providerName,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return PaymentInitializationResult::failure(
                'Payment initialization failed: ' . $e->getMessage(),
                'INITIALIZATION_ERROR'
            );
        }
    }

    /**
     * Ödeme callback'ini işler
     */
    public function handleCallback(string $providerName, Request $request): PaymentCallbackResult
    {
        try {
            $provider = $this->getProvider($providerName);

            Log::info('Payment callback received', [
                'provider' => $providerName,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'has_body' => !empty($request->getContent())
            ]);

            $result = $provider->handleCallback($request);

            if ($result->isSuccess()) {
                Log::info('Payment callback processed successfully', [
                    'provider' => $providerName,
                    'order_number' => $result->getOrderNumber(),
                    'transaction_id' => $result->getTransactionId(),
                    'amount' => $result->getAmount(),
                    'status' => $result->getStatus()
                ]);
            } else {
                Log::warning('Payment callback failed', [
                    'provider' => $providerName,
                    'order_number' => $result->getOrderNumber(),
                    'error_message' => $result->getErrorMessage(),
                    'error_code' => $result->getErrorCode()
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Payment callback error', [
                'provider' => $providerName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback error result
            return PaymentCallbackResult::failure(
                'unknown',
                'Callback processing failed: ' . $e->getMessage(),
                'CALLBACK_ERROR'
            );
        }
    }

    /**
     * İade işlemini gerçekleştirir
     */
    public function processRefund(string $providerName, Order $order, float $amount, ?string $reason = null): PaymentRefundResult
    {
        try {
            $provider = $this->getProvider($providerName);

            if (!$provider->canRefund()) {
                Log::warning('Refund not supported by provider', [
                    'provider' => $providerName,
                    'order_number' => $order->order_number
                ]);

                return PaymentRefundResult::failure(
                    $order->order_number,
                    $amount,
                    $order->currency_code ?? 'TRY',
                    "Provider '{$providerName}' does not support refunds",
                    'REFUND_NOT_SUPPORTED'
                );
            }

            Log::info('Refund process started', [
                'provider' => $providerName,
                'order_number' => $order->order_number,
                'amount' => $amount,
                'reason' => $reason
            ]);

            $result = $provider->processRefund($order, $amount, $reason);

            if ($result->isSuccess()) {
                Log::info('Refund processed successfully', [
                    'provider' => $providerName,
                    'order_number' => $result->getOrderNumber(),
                    'refund_transaction_id' => $result->getRefundTransactionId(),
                    'amount' => $result->getRefundAmount()
                ]);
            } else {
                Log::warning('Refund failed', [
                    'provider' => $providerName,
                    'order_number' => $result->getOrderNumber(),
                    'error_message' => $result->getErrorMessage(),
                    'error_code' => $result->getErrorCode()
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Refund error', [
                'provider' => $providerName,
                'order_number' => $order->order_number,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return PaymentRefundResult::failure(
                $order->order_number,
                $amount,
                $order->currency_code ?? 'TRY',
                'Refund processing failed: ' . $e->getMessage(),
                'REFUND_ERROR'
            );
        }
    }

    /**
     * Belirtilen para birimini destekleyen sağlayıcıları getirir
     */
    public function getProvidersForCurrency(string $currency): Collection
    {
        return $this->providers->filter(function (PaymentProviderInterface $provider) use ($currency) {
            return in_array($currency, $provider->getSupportedCurrencies());
        });
    }

    /**
     * Belirtilen ödeme metodunu destekleyen sağlayıcıları getirir
     */
    public function getProvidersForPaymentMethod(string $method): Collection
    {
        return $this->providers->filter(function (PaymentProviderInterface $provider) use ($method) {
            return in_array($method, $provider->getSupportedPaymentMethods());
        });
    }

    /**
     * Aktif (test modunda olmayan) sağlayıcıları getirir
     */
    public function getProductionProviders(): Collection
    {
        return $this->providers->filter(function (PaymentProviderInterface $provider) {
            return !$provider->isTestMode();
        });
    }

    /**
     * Test modundaki sağlayıcıları getirir
     */
    public function getTestProviders(): Collection
    {
        return $this->providers->filter(function (PaymentProviderInterface $provider) {
            return $provider->isTestMode();
        });
    }

    /**
     * Sistem durumu bilgisi
     */
    public function getStatus(): array
    {
        $status = [
            'total_providers' => $this->providers->count(),
            'production_providers' => $this->getProductionProviders()->count(),
            'test_providers' => $this->getTestProviders()->count(),
            'providers' => []
        ];

        foreach ($this->providers as $name => $provider) {
            $status['providers'][$name] = [
                'class' => get_class($provider),
                'test_mode' => $provider->isTestMode(),
                'can_refund' => $provider->canRefund(),
                'supported_currencies' => $provider->getSupportedCurrencies(),
                'supported_methods' => $provider->getSupportedPaymentMethods(),
                'configuration_valid' => $provider->validateConfiguration()
            ];
        }

        return $status;
    }
}