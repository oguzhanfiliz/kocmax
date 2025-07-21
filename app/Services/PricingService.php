<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Pricing\PricingServiceInterface;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Pricing\PriceEngine;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Collection;

class PricingService implements PricingServiceInterface
{
    private PriceEngine $priceEngine;

    public function __construct(PriceEngine $priceEngine)
    {
        $this->priceEngine = $priceEngine;
    }

    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult {
        return $this->priceEngine->calculatePrice($variant, $quantity, $customer, $context);
    }

    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null
    ): Collection {
        return $this->priceEngine->getAvailableDiscounts($variant, $customer);
    }

    public function validatePricing(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool {
        return $this->priceEngine->validatePricing($variant, $quantity, $customer);
    }

    public function bulkCalculatePrice(array $items, ?User $customer = null, array $context = []): Collection
    {
        return $this->priceEngine->bulkCalculatePrice($items, $customer, $context);
    }

    public function preCalculatePrices(ProductVariant $variant, ?User $customer = null): void
    {
        $this->priceEngine->preCalculatePrices($variant, $customer);
    }

    public function clearPriceCache(ProductVariant $variant, ?User $customer = null): void
    {
        $this->priceEngine->clearPriceCache($variant, $customer);
    }
}