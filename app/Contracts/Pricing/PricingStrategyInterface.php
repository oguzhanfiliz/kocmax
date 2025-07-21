<?php

declare(strict_types=1);

namespace App\Contracts\Pricing;

use App\Enums\CustomerType;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Price;
use App\ValueObjects\PriceResult;
use Illuminate\Support\Collection;

interface PricingStrategyInterface
{
    /**
     * Calculate the price for a product variant
     */
    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult;

    /**
     * Get the base price for this strategy
     */
    public function getBasePrice(ProductVariant $variant): Price;

    /**
     * Get available discounts for this strategy
     */
    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null,
        int $quantity = 1
    ): Collection;

    /**
     * Check if this strategy supports the given customer type
     */
    public function supports(CustomerType $customerType): bool;

    /**
     * Get the customer type this strategy handles
     */
    public function getCustomerType(): CustomerType;

    /**
     * Get strategy priority (higher number = higher priority)
     */
    public function getPriority(): int;

    /**
     * Validate if pricing can be calculated for given parameters
     */
    public function canCalculatePrice(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool;
}