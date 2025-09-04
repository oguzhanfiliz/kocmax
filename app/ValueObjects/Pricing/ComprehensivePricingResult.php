<?php

declare(strict_types=1);

namespace App\ValueObjects\Pricing;

/**
 * Kapsamlı fiyatlandırma sonucu value object'i
 * Tüm indirimler ve detaylar ile birlikte fiyat hesaplama sonucu
 */
class ComprehensivePricingResult
{
    public function __construct(
        private array $itemResults,
        private float $finalTotalPrice,
        private float $totalDiscount = 0,
        private array $appliedDiscounts = []
    ) {}

    public function getItemResults(): array
    {
        return $this->itemResults;
    }

    public function getFinalTotalPrice(): float
    {
        return $this->finalTotalPrice;
    }

    public function getSubtotal(): float
    {
        return $this->finalTotalPrice + $this->totalDiscount;
    }

    public function getTotalDiscount(): float
    {
        return $this->totalDiscount;
    }

    public function getAppliedDiscounts(): array
    {
        return $this->appliedDiscounts;
    }

    public function hasDiscounts(): bool
    {
        return $this->totalDiscount > 0;
    }

    public function getDiscountPercentage(): float
    {
        $subtotal = $this->getSubtotal();
        return $subtotal > 0 ? ($this->totalDiscount / $subtotal) * 100 : 0;
    }

    public function toArray(): array
    {
        return [
            'final_total_price' => $this->finalTotalPrice,
            'subtotal' => $this->getSubtotal(),
            'total_discount' => $this->totalDiscount,
            'discount_percentage' => round($this->getDiscountPercentage(), 2),
            'has_discounts' => $this->hasDiscounts(),
            'applied_discounts' => $this->appliedDiscounts,
            'items_count' => count($this->itemResults),
            'item_details' => array_map(fn($item) => $item->toArray(), $this->itemResults)
        ];
    }
}