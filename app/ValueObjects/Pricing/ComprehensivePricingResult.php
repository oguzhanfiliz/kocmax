<?php

declare(strict_types=1);

namespace App\ValueObjects\Pricing;

/**
 * Kapsamlı fiyatlandırma sonucu value object'i
 * Tüm indirimler ve detaylar ile birlikte fiyat hesaplama sonucu
 */
class ComprehensivePricingResult
{
    /**
     * Yapıcı metot.
     *
     * @param array $itemResults Kalem bazlı sonuç nesneleri listesi
     * @param float $finalTotalPrice İndirimler sonrası toplam fiyat
     * @param float $totalDiscount Toplam indirim tutarı
     * @param array $appliedDiscounts Uygulanan indirimlerin özet bilgileri
     */
    public function __construct(
        private array $itemResults,
        private float $finalTotalPrice,
        private float $totalDiscount = 0,
        private array $appliedDiscounts = []
    ) {}

    /**
     * Kalem bazlı sonuçları döndürür.
     *
     * @return array
     */
    public function getItemResults(): array
    {
        return $this->itemResults;
    }

    /**
     * Nihai toplam fiyatı döndürür.
     */
    public function getFinalTotalPrice(): float
    {
        return $this->finalTotalPrice;
    }

    /**
     * Ara toplamı döndürür (nihai fiyat + toplam indirim).
     */
    public function getSubtotal(): float
    {
        return $this->finalTotalPrice + $this->totalDiscount;
    }

    /**
     * Toplam indirim tutarını döndürür.
     */
    public function getTotalDiscount(): float
    {
        return $this->totalDiscount;
    }

    /**
     * Uygulanan indirimlerin özetini döndürür.
     *
     * @return array
     */
    public function getAppliedDiscounts(): array
    {
        return $this->appliedDiscounts;
    }

    /**
     * Herhangi bir indirim var mı?
     */
    public function hasDiscounts(): bool
    {
        return $this->totalDiscount > 0;
    }

    /**
     * Toplam indirim yüzdesini döndürür.
     */
    public function getDiscountPercentage(): float
    {
        $subtotal = $this->getSubtotal();
        return $subtotal > 0 ? ($this->totalDiscount / $subtotal) * 100 : 0;
    }

    /**
     * Dizi temsili döndürür (loglama/JSON vb.).
     *
     * @return array<string,mixed>
     */
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