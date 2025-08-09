<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Currency;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyConversionService
{
    private const CACHE_KEY_PREFIX = 'currency_conversion';
    private const CACHE_DURATION = 3600; // 1 hour

    /**
     * Convert product variant price to target currency
     * Uses TCMB exchange rates for real-time conversion
     */
    public function convertVariantPrice(
        ProductVariant $variant, 
        string $targetCurrency = 'TRY'
    ): float {
        $sourceCurrency = $variant->getSourceCurrency();
        $sourcePrice = $variant->getSourcePrice();
        
        if ($sourceCurrency === $targetCurrency) {
            return $sourcePrice;
        }

        $cacheKey = $this->getCacheKey($sourceCurrency, $targetCurrency);
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($variant, $targetCurrency) {
            return $this->performConversion($variant, $targetCurrency);
        });
    }

    /**
     * Get real-time exchange rate from TCMB
     */
    public function getRealTimeExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        // Try to get from TCMB first
        try {
            $tcmbService = app(TcmbExchangeRateService::class);
            
            // Check if TCMB has fresh rates
            $cachedRates = $tcmbService->getCachedRates();
            if (!$cachedRates) {
                // Update rates from TCMB
                $tcmbService->updateRates();
            }
            
            // Get updated currency rates from database
            $fromCurrencyModel = Currency::where('code', $fromCurrency)->first();
            $toCurrencyModel = Currency::where('code', $toCurrency)->first();
            
            if ($fromCurrencyModel && $toCurrencyModel) {
                return $fromCurrencyModel->convertTo(1.0, $toCurrencyModel);
            }
            
        } catch (\Exception $e) {
            Log::warning('TCMB exchange rate fetch failed, using cached rates', [
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'error' => $e->getMessage()
            ]);
        }

        // Fallback to cached rates
        return $this->getExchangeRate($fromCurrency, $toCurrency);
    }

    /**
     * Convert multiple variant prices efficiently
     */
    public function convertBulkPrices(array $variants, string $targetCurrency = 'TRY'): array
    {
        $results = [];
        $currencies = $this->getUniqueCurrencies($variants);
        
        // Pre-load exchange rates
        $exchangeRates = $this->getExchangeRates($currencies, $targetCurrency);
        
        foreach ($variants as $variant) {
            $results[$variant->id] = $this->convertWithPreloadedRates(
                $variant, 
                $targetCurrency, 
                $exchangeRates
            );
        }
        
        return $results;
    }

    /**
     * Get current exchange rate between currencies
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $cacheKey = $this->getCacheKey($fromCurrency, $toCurrency);
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($fromCurrency, $toCurrency) {
            $from = Currency::where('code', $fromCurrency)->first();
            $to = Currency::where('code', $toCurrency)->first();
            
            if (!$from || !$to) {
                Log::warning('Currency not found for conversion', [
                    'from' => $fromCurrency,
                    'to' => $toCurrency
                ]);
                return 1.0;
            }
            
            return $from->convertTo(1.0, $to);
        });
    }

    /**
     * Format price with currency symbol
     */
    public function formatPrice(float $amount, string $currencyCode): string
    {
        $currency = Currency::where('code', $currencyCode)->first();
        
        if (!$currency) {
            return number_format($amount, 2) . ' ' . $currencyCode;
        }
        
        return $currency->symbol . number_format($amount, 2);
    }

    /**
     * Get available currencies for selection
     */
    public function getAvailableCurrencies(): array
    {
        return Cache::remember('available_currencies', self::CACHE_DURATION, function () {
            return Currency::where('is_active', true)
                ->orderBy('is_default', 'desc')
                ->orderBy('name')
                ->get()
                ->map(function ($currency) {
                    return [
                        'code' => $currency->code,
                        'name' => $currency->name,
                        'symbol' => $currency->symbol,
                        'is_default' => $currency->is_default,
                        'exchange_rate' => $currency->exchange_rate
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Clear currency conversion cache
     */
    public function clearCache(): void
    {
        Cache::forget('available_currencies');
        // Note: Individual conversion cache keys are auto-expired
    }

    /**
     * Perform actual currency conversion
     */
    private function performConversion(ProductVariant $variant, string $targetCurrency): float
    {
        try {
            $sourceCurrencyCode = $variant->getSourceCurrency();
            $sourcePrice = $variant->getSourcePrice();
            
            $sourceCurrency = Currency::where('code', $sourceCurrencyCode)->first();
            $targetCurrencyModel = Currency::where('code', $targetCurrency)->first();
            
            if (!$sourceCurrency || !$targetCurrencyModel) {
                Log::warning('Currency conversion failed - currency not found', [
                    'variant_id' => $variant->id,
                    'source_currency' => $sourceCurrencyCode,
                    'target_currency' => $targetCurrency
                ]);
                return $sourcePrice;
            }
            
            return $sourceCurrency->convertTo($sourcePrice, $targetCurrencyModel);
            
        } catch (\Exception $e) {
            Log::error('Currency conversion error', [
                'variant_id' => $variant->id,
                'error' => $e->getMessage()
            ]);
            return $variant->getSourcePrice(); // Fallback to source price
        }
    }

    /**
     * Convert with preloaded exchange rates for bulk operations
     */
    private function convertWithPreloadedRates(
        ProductVariant $variant, 
        string $targetCurrency, 
        array $exchangeRates
    ): float {
        $sourceCurrency = $variant->getSourceCurrency();
        $sourcePrice = $variant->getSourcePrice();
        
        $rateKey = $sourceCurrency . '_' . $targetCurrency;
        
        if (!isset($exchangeRates[$rateKey])) {
            return $sourcePrice;
        }
        
        return $sourcePrice * $exchangeRates[$rateKey];
    }

    /**
     * Get unique currencies from variants
     */
    private function getUniqueCurrencies(array $variants): array
    {
        $currencies = collect($variants)->map(function ($variant) {
            return $variant->getSourceCurrency();
        })->unique()->toArray();
        
        $currencies[] = 'TRY'; // Always include base currency
        return array_unique($currencies);
    }

    /**
     * Get exchange rates for bulk operations
     */
    private function getExchangeRates(array $currencies, string $targetCurrency): array
    {
        $rates = [];
        
        foreach ($currencies as $currency) {
            if ($currency !== $targetCurrency) {
                $rates[$currency . '_' . $targetCurrency] = $this->getExchangeRate($currency, $targetCurrency);
            }
        }
        
        return $rates;
    }

    /**
     * Generate cache key for currency conversion
     */
    private function getCacheKey(string $fromCurrency, string $toCurrency): string
    {
        return self::CACHE_KEY_PREFIX . '_' . strtolower($fromCurrency) . '_' . strtolower($toCurrency);
    }
}
