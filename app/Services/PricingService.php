<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\SettingHelper;
use App\Interfaces\Pricing\PricingServiceInterface;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Pricing\PriceEngine;
use App\ValueObjects\Pricing\Price;
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
        $priceResult = $this->priceEngine->calculatePrice($variant, $quantity, $customer, $context);

        $taxRate = $this->resolveTaxRate($variant);
        $unitTaxAmount = $this->calculateUnitTaxAmount($priceResult->getFinalPrice(), $taxRate);

        return $priceResult->withTax($taxRate, $unitTaxAmount);
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

    private function resolveTaxRate(ProductVariant $variant): float
    {
        $variant->loadMissing([
            'product.categories' => function ($query) {
                $query->select('categories.id', 'categories.name', 'categories.slug', 'categories.tax_rate');
            },
            'product'
        ]);

        $product = $variant->product;

        if ($product && $product->tax_rate !== null) {
            return (float) $product->tax_rate;
        }

        if ($product) {
            $categoryWithTax = $product->categories
                ->first(fn($category) => $category->tax_rate !== null);

            if ($categoryWithTax) {
                return (float) $categoryWithTax->tax_rate;
            }
        }

        return (float) SettingHelper::get('pricing.default_tax_rate', 0.0);
    }

    private function calculateUnitTaxAmount(Price $unitFinalPrice, float $taxRate): Price
    {
        $normalizedRate = max(0.0, $taxRate);

        if ($normalizedRate === 0.0 || $unitFinalPrice->isZero()) {
            return new Price(0.0, $unitFinalPrice->getCurrency());
        }

        $amount = round($unitFinalPrice->getAmount() * ($normalizedRate / 100), 2);

        return new Price($amount, $unitFinalPrice->getCurrency());
    }
}
