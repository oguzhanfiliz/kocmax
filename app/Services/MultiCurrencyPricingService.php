<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductVariant;
use App\Services\CurrencyConversionService;
use App\Services\Pricing\PriceEngine;
use App\ValueObjects\Pricing\Price;
use App\ValueObjects\Pricing\PriceResult;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MultiCurrencyPricingService
{
    private PriceEngine $priceEngine;
    private CurrencyConversionService $currencyService;
    
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'multi_currency_pricing';

    public function __construct(
        PriceEngine $priceEngine,
        CurrencyConversionService $currencyService
    ) {
        $this->priceEngine = $priceEngine;
        $this->currencyService = $currencyService;
    }

    /**
     * Calculate price with currency conversion
     */
    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        string $targetCurrency = 'TRY',
        array $context = []
    ): PriceResult {
        $cacheKey = $this->getCacheKey($variant->id, $quantity, $customer?->id, $targetCurrency);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use (
            $variant, $quantity, $customer, $targetCurrency, $context
        ) {
            return $this->performPriceCalculation($variant, $quantity, $customer, $targetCurrency, $context);
        });
    }

    /**
     * Calculate bulk prices with currency conversion
     */
    public function calculateBulkPrices(
        array $items, // [['variant' => ProductVariant, 'quantity' => int], ...]
        ?User $customer = null,
        string $targetCurrency = 'TRY',
        array $context = []
    ): array {
        $results = [];
        
        // Group by source currency for efficient conversion
        $currencyGroups = $this->groupItemsByCurrency($items);
        
        foreach ($currencyGroups as $sourceCurrency => $currencyItems) {
            $exchangeRate = $this->currencyService->getExchangeRate($sourceCurrency, $targetCurrency);
            
            foreach ($currencyItems as $item) {
                $variant = $item['variant'];
                $quantity = $item['quantity'];
                
                // Calculate base price using source currency price
                $basePriceResult = $this->priceEngine->calculatePrice($variant, $quantity, $customer, $context);
                
                // Convert to target currency if needed
                $convertedResult = ($sourceCurrency === $targetCurrency) 
                    ? $basePriceResult 
                    : $this->performPriceResultConversion($basePriceResult, $exchangeRate, $targetCurrency);
                
                $results[] = [
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'price_result' => $convertedResult,
                    'source_currency' => $sourceCurrency,
                    'target_currency' => $targetCurrency,
                    'exchange_rate' => $exchangeRate
                ];
            }
        }
        
        return $results;
    }

    /**
     * Get available currencies with current rates
     */
    public function getAvailableCurrencies(): array
    {
        return $this->currencyService->getAvailableCurrencies();
    }

    /**
     * Format price with proper currency display
     */
    public function formatPrice(float $amount, string $currency): string
    {
        return $this->currencyService->formatPrice($amount, $currency);
    }

    /**
     * Convert price result to different currency
     */
    public function convertPriceResult(PriceResult $priceResult, string $targetCurrency): PriceResult
    {
        $originalCurrency = $priceResult->getFinalPrice()->getCurrency();
        
        if ($originalCurrency === $targetCurrency) {
            return $priceResult;
        }
        
        $exchangeRate = $this->currencyService->getExchangeRate($originalCurrency, $targetCurrency);
        
        return $this->performPriceResultConversion($priceResult, $exchangeRate, $targetCurrency);
    }

    /**
     * Get real-time exchange rates
     */
    public function getExchangeRates(array $currencies, string $baseCurrency = 'TRY'): array
    {
        $rates = [];
        
        foreach ($currencies as $currency) {
            if ($currency !== $baseCurrency) {
                $rates[$currency] = $this->currencyService->getRealTimeExchangeRate($baseCurrency, $currency);
            }
        }
        
        return $rates;
    }

    /**
     * Simple currency conversion method for API resources
     */
    public function convertPrice(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        $exchangeRate = $this->currencyService->getExchangeRate($fromCurrency, $toCurrency);
        return $amount * $exchangeRate;
    }

    /**
     * Clear pricing cache for specific variant
     */
    public function clearVariantCache(int $variantId): void
    {
        $cachePattern = self::CACHE_PREFIX . "_variant_{$variantId}_*";
        
        // Note: In production, use Redis for pattern-based cache clearing
        // Cache::flush(); // Use this for simple approach or implement pattern clearing
    }

    /**
     * Perform actual price calculation with currency conversion
     */
    private function performPriceCalculation(
        ProductVariant $variant,
        int $quantity,
        ?User $customer,
        string $targetCurrency,
        array $context
    ): PriceResult {
        try {
            // Get base price calculation
            $basePriceResult = $this->priceEngine->calculatePrice($variant, $quantity, $customer, $context);
            
            // Get source currency (from variant's source_currency field)
            $sourceCurrency = $variant->getSourceCurrency();
            
            // Convert if different currency
            if ($sourceCurrency !== $targetCurrency) {
                $exchangeRate = $this->currencyService->getRealTimeExchangeRate($sourceCurrency, $targetCurrency);
                return $this->performPriceResultConversion($basePriceResult, $exchangeRate, $targetCurrency);
            }
            
            return $basePriceResult;
            
        } catch (\Exception $e) {
            Log::error('Multi-currency price calculation failed', [
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'customer_id' => $customer?->id,
                'target_currency' => $targetCurrency,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Convert PriceResult to different currency with exchange rate
     */
    private function performPriceResultConversion(PriceResult $priceResult, float $exchangeRate, string $targetCurrency): PriceResult
    {
        // Convert base price
        $originalAmount = $priceResult->getFinalPrice()->getAmount();
        $convertedAmount = $originalAmount * $exchangeRate;
        $convertedPrice = new Price($convertedAmount, $targetCurrency);
        
        // Convert subtotal if exists
        $originalSubtotal = $priceResult->getSubtotal() ? $priceResult->getSubtotal()->getAmount() : 0;
        $convertedSubtotal = new Price($originalSubtotal * $exchangeRate, $targetCurrency);
        
        // Convert discounts
        $convertedDiscounts = [];
        foreach ($priceResult->getAppliedDiscounts() as $discount) {
            $convertedDiscountAmount = $discount->getAmount()->getAmount() * $exchangeRate;
            $convertedDiscounts[] = $discount->withAmount(new Price($convertedDiscountAmount, $targetCurrency));
        }
        
        // Create new PriceResult with converted values
        return new PriceResult(
            finalPrice: $convertedPrice,
            subtotal: $convertedSubtotal,
            appliedDiscounts: $convertedDiscounts,
            customerType: $priceResult->getCustomerType(),
            calculations: array_merge($priceResult->getCalculations(), [
                'currency_conversion' => [
                    'original_currency' => $priceResult->getFinalPrice()->getCurrency(),
                    'target_currency' => $targetCurrency,
                    'exchange_rate' => $exchangeRate,
                    'conversion_time' => now()->toISOString()
                ]
            ])
        );
    }

    /**
     * Group items by their source currency
     */
    private function groupItemsByCurrency(array $items): array
    {
        $groups = [];
        
        foreach ($items as $item) {
            $variant = $item['variant'];
            $sourceCurrency = $variant->getSourceCurrency();
            
            if (!isset($groups[$sourceCurrency])) {
                $groups[$sourceCurrency] = [];
            }
            
            $groups[$sourceCurrency][] = $item;
        }
        
        return $groups;
    }

    /**
     * Generate cache key for price calculation
     */
    private function getCacheKey(int $variantId, int $quantity, ?int $customerId, string $currency): string
    {
        $customerKey = $customerId ? "user_{$customerId}" : 'guest';
        return implode('_', [
            self::CACHE_PREFIX,
            "variant_{$variantId}",
            "qty_{$quantity}",
            $customerKey,
            "currency_{$currency}"
        ]);
    }
}