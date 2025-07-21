<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Contracts\Pricing\PricingStrategyInterface;
use App\Enums\Pricing\CustomerType;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\Discount;
use App\ValueObjects\Pricing\Price;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class AbstractPricingStrategy implements PricingStrategyInterface
{
    protected CustomerType $customerType;
    protected int $priority;

    public function __construct(CustomerType $customerType, int $priority = 0)
    {
        $this->customerType = $customerType;
        $this->priority = $priority;
    }

    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult {
        $this->validateInputs($variant, $quantity);

        $basePrice = $this->getBasePrice($variant);
        $availableDiscounts = $this->getAvailableDiscounts($variant, $customer, $quantity);
        
        $finalPrice = $this->applyDiscounts($basePrice, $availableDiscounts, $quantity);
        $appliedDiscounts = $this->getAppliedDiscounts($basePrice, $availableDiscounts, $quantity);

        return new PriceResult(
            originalPrice: $basePrice,
            finalPrice: $finalPrice,
            appliedDiscounts: $appliedDiscounts,
            customerType: $this->customerType,
            quantity: $quantity,
            metadata: array_merge($context, [
                'strategy' => static::class,
                'calculation_timestamp' => now()->timestamp
            ])
        );
    }

    public function supports(CustomerType $customerType): bool
    {
        return $this->customerType === $customerType;
    }

    public function getCustomerType(): CustomerType
    {
        return $this->customerType;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function canCalculatePrice(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool {
        try {
            $this->validateInputs($variant, $quantity);
            return $this->getBasePrice($variant)->getAmount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function validateInputs(ProductVariant $variant, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than 0');
        }

        if (!$variant->is_active) {
            throw new InvalidArgumentException('Cannot calculate price for inactive variant');
        }
    }

    protected function applyDiscounts(Price $basePrice, Collection $discounts, int $quantity): Price
    {
        $currentPrice = $basePrice;

        // Sort discounts by priority (highest first)
        $sortedDiscounts = $discounts->sortByDesc(fn(Discount $discount) => $discount->getPriority());

        foreach ($sortedDiscounts as $discount) {
            if ($discount->canApplyTo($currentPrice, $quantity)) {
                $currentPrice = $discount->apply($currentPrice);
            }
        }

        return $currentPrice;
    }

    protected function getAppliedDiscounts(Price $basePrice, Collection $discounts, int $quantity): Collection
    {
        $appliedDiscounts = collect();
        $currentPrice = $basePrice;

        // Sort discounts by priority (highest first)
        $sortedDiscounts = $discounts->sortByDesc(fn(Discount $discount) => $discount->getPriority());

        foreach ($sortedDiscounts as $discount) {
            if ($discount->canApplyTo($currentPrice, $quantity)) {
                $appliedDiscounts->push($discount);
                $currentPrice = $discount->apply($currentPrice);
            }
        }

        return $appliedDiscounts;
    }

    /**
     * Get customer-specific discounts (implemented by child classes)
     */
    abstract protected function getCustomerDiscounts(ProductVariant $variant, ?User $customer = null, int $quantity = 1): Collection;

    /**
     * Get bulk discounts based on quantity
     */
    protected function getBulkDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Get bulk discounts from database
        $bulkDiscounts = \App\Models\BulkDiscount::active()
            ->forProduct($variant->product_id)
            ->forQuantity($quantity)
            ->get();

        foreach ($bulkDiscounts as $bulkDiscount) {
            $discounts->push(
                Discount::percentage(
                    $bulkDiscount->discount_percentage,
                    'Bulk Discount',
                    "Get {$bulkDiscount->discount_percentage}% off for {$quantity}+ items",
                    100 // High priority for bulk discounts
                )
            );
        }

        return $discounts;
    }

    /**
     * Get category-based discounts
     */
    protected function getCategoryDiscounts(ProductVariant $variant, ?User $customer = null): Collection
    {
        $discounts = collect();

        if (!$customer) {
            return $discounts;
        }

        // Get category discounts for dealer customers
        if ($customer->hasRole('dealer')) {
            $categoryDiscounts = \App\Models\DealerDiscount::active()
                ->forDealer($customer->id)
                ->whereHas('category', function ($query) use ($variant) {
                    $categoryIds = $variant->product->categories()->pluck('categories.id');
                    $query->whereIn('id', $categoryIds);
                })
                ->get();

            foreach ($categoryDiscounts as $categoryDiscount) {
                $discounts->push(
                    $categoryDiscount->discount_type === 'percentage' 
                        ? Discount::percentage(
                            $categoryDiscount->discount_value,
                            'Category Discount',
                            "Dealer category discount",
                            90 // High priority but lower than bulk
                        )
                        : Discount::fixedAmount(
                            $categoryDiscount->discount_value,
                            'Category Discount',
                            "Dealer category discount",
                            90
                        )
                );
            }
        }

        return $discounts;
    }
}