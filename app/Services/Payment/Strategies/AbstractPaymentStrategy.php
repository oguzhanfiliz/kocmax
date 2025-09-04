<?php

declare(strict_types=1);

namespace App\Services\Payment\Strategies;

use App\Contracts\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Log;

/**
 * Ödeme strategy'leri için temel abstract sınıf
 * Ortak fonksiyonları ve standart davranışları sağlar
 */
abstract class AbstractPaymentStrategy implements PaymentProviderInterface
{
    protected bool $testMode;
    protected array $supportedCurrencies;
    protected array $supportedPaymentMethods;

    public function __construct(bool $testMode = false)
    {
        $this->testMode = $testMode;
        $this->supportedCurrencies = ['TRY']; // Varsayılan
        $this->supportedPaymentMethods = ['card']; // Varsayılan
    }

    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    public function getSupportedPaymentMethods(): array
    {
        return $this->supportedPaymentMethods;
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    public function canRefund(): bool
    {
        return true; // Varsayılan olarak iade desteklenir
    }

    public function validateConfiguration(): bool
    {
        return true; // Override edilmeli
    }

    /**
     * Ödeme için gerekli alanları doğrular
     */
    protected function validateRequiredFields(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Required field '{$field}' is missing or empty");
            }
        }
    }

    /**
     * Mock payment gateway çağrısı (development ve test için)
     */
    protected function mockPaymentGatewayCall(array $data = []): bool
    {
        // %95 başarı oranı simülasyonu
        $success = mt_rand(1, 100) <= 95;
        
        Log::info('Mock payment gateway call', [
            'provider' => $this->getProviderName(),
            'success' => $success,
            'test_mode' => $this->testMode,
            'data_keys' => array_keys($data)
        ]);
        
        return $success;
    }

    /**
     * Transaction ID oluşturucu
     */
    protected function generateTransactionId(string $prefix = 'TXN'): string
    {
        $timestamp = time();
        $random = strtoupper(substr(md5(uniqid()), 0, 8));
        
        return "{$prefix}_{$timestamp}_{$random}";
    }

    /**
     * Para birimi doğrulama
     */
    protected function validateCurrency(string $currency): void
    {
        if (!in_array($currency, $this->getSupportedCurrencies())) {
            throw new \InvalidArgumentException(
                "Currency '{$currency}' is not supported by " . $this->getProviderName()
            );
        }
    }
}