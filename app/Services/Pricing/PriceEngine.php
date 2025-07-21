<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Contracts\Pricing\PricingStrategyInterface;
use App\Enums\Pricing\CustomerType;
use App\Exceptions\Pricing\InvalidPriceException;
use App\Exceptions\Pricing\PricingException;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PriceEngine
{
    /** @var Collection<PricingStrategyInterface> */
    private Collection $strategies;
    
    private CustomerTypeDetector $customerTypeDetector;
    
    private bool $cachingEnabled;
    private int $cacheLifetime;

    public function __construct(
        CustomerTypeDetector $customerTypeDetector,
        bool $cachingEnabled = true,
        int $cacheLifetime = 300 // 5 minutes
    ) {
        $this->strategies = collect();
        $this->customerTypeDetector = $customerTypeDetector;
        $this->cachingEnabled = $cachingEnabled;
        $this->cacheLifetime = $cacheLifetime;
    }

    public function addStrategy(PricingStrategyInterface $strategy): self
    {
        $this->strategies->push($strategy);
        
        // Sort strategies by priority (highest first)
        $this->strategies = $this->strategies->sortByDesc(fn($strategy) => $strategy->getPriority());
        
        return $this;
    }

    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult {
        try {
            $customerType = $this->customerTypeDetector->detect($customer);
            
            if ($this->cachingEnabled) {
                $cacheKey = $this->generateCacheKey($variant, $quantity, $customer, $context);
                
                return Cache::remember($cacheKey, $this->cacheLifetime, function () use ($variant, $quantity, $customer, $context, $customerType) {
                    return $this->performPriceCalculation($variant, $quantity, $customer, $context, $customerType);
                });
            }
            
            return $this->performPriceCalculation($variant, $quantity, $customer, $context, $customerType);
            
        } catch (\Exception $e) {
            Log::error('Price calculation failed', [
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'customer_id' => $customer?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new PricingException(
                "Failed to calculate price for variant {$variant->id}: {$e->getMessage()}",
                previous: $e
            );
        }
    }

    private function performPriceCalculation(
        ProductVariant $variant,
        int $quantity,
        ?User $customer,
        array $context,
        CustomerType $customerType
    ): PriceResult {
        $strategy = $this->getStrategyForCustomerType($customerType);
        
        if (!$strategy) {
            throw new InvalidPriceException("No pricing strategy found for customer type: {$customerType->value}");
        }

        if (!$strategy->canCalculatePrice($variant, $quantity, $customer)) {
            throw new InvalidPriceException("Cannot calculate price for the given parameters");
        }

        $startTime = microtime(true);
        
        $result = $strategy->calculatePrice($variant, $quantity, $customer, $context);
        
        $calculationTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        // Add performance metadata
        $result = $result->withMetadata('calculation_time_ms', round($calculationTime, 2));
        $result = $result->withMetadata('strategy_used', get_class($strategy));
        $result = $result->withMetadata('customer_tier', $this->customerTypeDetector->getCustomerTier($customer));
        
        // Log performance if calculation takes too long
        if ($calculationTime > 100) { // 100ms threshold
            Log::warning('Slow price calculation detected', [
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'customer_id' => $customer?->id,
                'calculation_time_ms' => $calculationTime,
                'strategy' => get_class($strategy)
            ]);
        }
        
        return $result;
    }

    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null,
        int $quantity = 1
    ): Collection {
        $customerType = $this->customerTypeDetector->detect($customer);
        $strategy = $this->getStrategyForCustomerType($customerType);
        
        if (!$strategy) {
            return collect();
        }
        
        return $strategy->getAvailableDiscounts($variant, $customer, $quantity);
    }

    public function validatePricing(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool {
        try {
            $customerType = $this->customerTypeDetector->detect($customer);
            $strategy = $this->getStrategyForCustomerType($customerType);
            
            return $strategy?->canCalculatePrice($variant, $quantity, $customer) ?? false;
            
        } catch (\Exception $e) {
            Log::warning('Price validation failed', [
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'customer_id' => $customer?->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    public function bulkCalculatePrice(array $items, ?User $customer = null, array $context = []): Collection
    {
        $results = collect();
        
        foreach ($items as $item) {
            try {
                $variant = $item['variant'] ?? null;
                $quantity = $item['quantity'] ?? 1;
                
                if (!$variant instanceof ProductVariant) {
                    continue;
                }
                
                $result = $this->calculatePrice($variant, $quantity, $customer, $context);
                $results->push([
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'price_result' => $result
                ]);
                
            } catch (\Exception $e) {
                Log::error('Bulk price calculation item failed', [
                    'item' => $item,
                    'customer_id' => $customer?->id,
                    'error' => $e->getMessage()
                ]);
                
                // Continue with other items even if one fails
                continue;
            }
        }
        
        return $results;
    }

    public function preCalculatePrices(ProductVariant $variant, ?User $customer = null): void
    {
        if (!$this->cachingEnabled) {
            return;
        }
        
        // Pre-calculate common quantities
        $commonQuantities = [1, 5, 10, 25, 50, 100];
        
        foreach ($commonQuantities as $quantity) {
            try {
                $this->calculatePrice($variant, $quantity, $customer);
            } catch (\Exception $e) {
                // Ignore errors during pre-calculation
                Log::debug('Pre-calculation failed', [
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'customer_id' => $customer?->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function clearPriceCache(ProductVariant $variant, ?User $customer = null): void
    {
        if (!$this->cachingEnabled) {
            return;
        }
        
        $pattern = "price_calculation:{$variant->id}:*";
        if ($customer) {
            $pattern = "price_calculation:{$variant->id}:{$customer->id}:*";
        }
        
        // Clear cache entries matching the pattern
        $keys = Cache::getStore()->getRedis()->keys($pattern);
        if (!empty($keys)) {
            Cache::getStore()->getRedis()->del(...$keys);
        }
    }

    private function getStrategyForCustomerType(CustomerType $customerType): ?PricingStrategyInterface
    {
        return $this->strategies->first(fn(PricingStrategyInterface $strategy) => $strategy->supports($customerType));
    }

    private function generateCacheKey(ProductVariant $variant, int $quantity, ?User $customer, array $context): string
    {
        $customerKey = $customer ? $customer->id : 'guest';
        $contextHash = md5(serialize($context));
        
        return "price_calculation:{$variant->id}:{$customerKey}:{$quantity}:{$contextHash}";
    }

    public function getRegisteredStrategies(): Collection
    {
        return $this->strategies;
    }

    public function hasStrategyFor(CustomerType $customerType): bool
    {
        return $this->getStrategyForCustomerType($customerType) !== null;
    }

    public function enableCaching(int $lifetime = 300): self
    {
        $this->cachingEnabled = true;
        $this->cacheLifetime = $lifetime;
        
        return $this;
    }

    public function disableCaching(): self
    {
        $this->cachingEnabled = false;
        
        return $this;
    }
}