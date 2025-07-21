<?php

declare(strict_types=1);

namespace App\Interfaces\Pricing;

use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Collection;

interface PricingServiceInterface
{
    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult;

    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null
    ): Collection;

    public function validatePricing(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool;
}
