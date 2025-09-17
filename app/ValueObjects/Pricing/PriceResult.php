<?php

declare(strict_types=1);

namespace App\ValueObjects\Pricing;

use App\Enums\Pricing\CustomerType;
use Illuminate\Support\Collection;

/**
 * Fiyatlandırma sonucu değer nesnesi.
 *
 * Orijinal/nihai fiyat, uygulanan indirimler, toplam indirim miktarı,
 * müşteri tipi ve adet bilgisi ile metaveriyi taşır.
 */
class PriceResult
{
    private readonly Price $originalPrice;
    private readonly Price $finalPrice;
    private readonly Collection $appliedDiscounts;
    private readonly Price $totalDiscountAmount;
    private readonly CustomerType $customerType;
    private readonly int $quantity;
    private readonly array $metadata;
    private readonly float $taxRate;
    private readonly Price $taxAmount;
    private readonly Price $finalPriceWithTax;

    /**
     * Yapıcı metot.
     *
     * @param Price $originalPrice Orijinal fiyat
     * @param Price $finalPrice Nihai fiyat
     * @param Collection $appliedDiscounts Uygulanan indirimler koleksiyonu
     * @param CustomerType $customerType Müşteri tipi
     * @param int $quantity Adet
     * @param array $metadata Ek metaveriler
     */
    public function __construct(
        Price $originalPrice,
        Price $finalPrice,
        Collection $appliedDiscounts,
        CustomerType $customerType,
        int $quantity = 1,
        array $metadata = [],
        float $taxRate = 0.0,
        ?Price $taxAmount = null
    ) {
        $this->originalPrice = $originalPrice;
        $this->finalPrice = $finalPrice;
        $this->appliedDiscounts = $appliedDiscounts;
        $this->customerType = $customerType;
        $this->quantity = $quantity;
        $this->metadata = $metadata;

        // Toplam indirim tutarını hesapla
        $this->totalDiscountAmount = $originalPrice->subtract($finalPrice);

        $this->taxRate = max(0, $taxRate);
        $this->taxAmount = $taxAmount ?? new Price(0.0, $finalPrice->getCurrency());
        $this->finalPriceWithTax = $finalPrice->add($this->taxAmount);
    }

    /**
     * Orijinal fiyatı döndürür.
     */
    public function getOriginalPrice(): Price
    {
        return $this->originalPrice;
    }

    /**
     * Nihai fiyatı döndürür.
     */
    public function getFinalPrice(): Price
    {
        return $this->finalPrice;
    }

    /**
     * Uygulanan indirimler listesini döndürür.
     *
     * @return Collection<Discount>
     */
    public function getAppliedDiscounts(): Collection
    {
        return $this->appliedDiscounts;
    }

    /**
     * Toplam indirim miktarını döndürür.
     */
    public function getTotalDiscountAmount(): Price
    {
        return $this->totalDiscountAmount;
    }

    /**
     * Müşteri tipini döndürür.
     */
    public function getCustomerType(): CustomerType
    {
        return $this->customerType;
    }

    /**
     * Adeti döndürür.
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Ek metaverileri döndürür.
     *
     * @return array<string,mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Uygulanan KDV oranı.
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * Birim başına KDV tutarı.
     */
    public function getTaxAmount(): Price
    {
        return $this->taxAmount;
    }

    /**
     * Toplam KDV tutarı (adet ile çarpılmış).
     */
    public function getTotalTaxAmount(): Price
    {
        return $this->taxAmount->multiply($this->quantity);
    }

    /**
     * Birim fiyat (KDV dahil).
     */
    public function getUnitFinalPriceWithTax(): Price
    {
        return $this->finalPriceWithTax;
    }

    /**
     * Toplam fiyat (KDV dahil, adet ile çarpılmış).
     */
    public function getTotalFinalPriceWithTax(): Price
    {
        return $this->finalPriceWithTax->multiply($this->quantity);
    }

    /**
     * Herhangi bir indirim uygulanmış mı?
     */
    public function hasDiscounts(): bool
    {
        return $this->appliedDiscounts->isNotEmpty();
    }

    /**
     * Uygulanan indirim sayısını döndürür.
     */
    public function getDiscountCount(): int
    {
        return $this->appliedDiscounts->count();
    }

    /**
     * Toplam indirim yüzdesini döndürür.
     */
    public function getTotalDiscountPercentage(): float
    {
        if ($this->originalPrice->isZero()) {
            return 0.0;
        }

        return ($this->totalDiscountAmount->getAmount() / $this->originalPrice->getAmount()) * 100;
    }

    /**
     * Birim başına orijinal fiyat.
     */
    public function getUnitOriginalPrice(): Price
    {
        return $this->originalPrice;
    }

    /**
     * Birim başına nihai fiyat.
     */
    public function getUnitFinalPrice(): Price
    {
        return $this->finalPrice;
    }

    /**
     * Toplam orijinal fiyat (adet ile çarpılmış).
     */
    public function getTotalOriginalPrice(): Price
    {
        return $this->originalPrice->multiply($this->quantity);
    }

    /**
     * Toplam nihai fiyat (adet ile çarpılmış).
     */
    public function getTotalFinalPrice(): Price
    {
        return $this->finalPrice->multiply($this->quantity);
    }

    /**
     * Toplam tasarruf tutarı (indirimin parasal karşılığı).
     */
    public function getSavings(): Price
    {
        return $this->totalDiscountAmount;
    }

    /**
     * Tasarruf yüzdesi (toplam indirim yüzdesi ile eşdeğer).
     */
    public function getSavingsPercentage(): float
    {
        return $this->getTotalDiscountPercentage();
    }

    /**
     * Verilen tipe sahip indirimleri döndürür.
     *
     * @param string $type İndirim tipi
     * @return Collection<Discount>
     */
    public function getDiscountsByType(string $type): Collection
    {
        return $this->appliedDiscounts->filter(
            fn(Discount $discount) => $discount->getType() === $type
        );
    }

    /**
     * Verilen tipe ait en az bir indirim var mı?
     */
    public function hasDiscountType(string $type): bool
    {
        return $this->getDiscountsByType($type)->isNotEmpty();
    }

    /**
     * En yüksek öncelikli indirimi döndürür.
     */
    public function getHighestPriorityDiscount(): ?Discount
    {
        return $this->appliedDiscounts
            ->sortByDesc('priority')
            ->first();
    }

    /**
     * Metaveriye bir anahtar/değer ekleyerek yeni bir sonuç nesnesi döndürür.
     */
    public function withMetadata(string $key, mixed $value): self
    {
        $newMetadata = array_merge($this->metadata, [$key => $value]);
        
        return new self(
            $this->originalPrice,
            $this->finalPrice,
            $this->appliedDiscounts,
            $this->customerType,
            $this->quantity,
            $newMetadata,
            $this->taxRate,
            $this->taxAmount
        );
    }

    /**
     * KDV bilgisi ile yeni bir sonuç nesnesi döndürür.
     */
    public function withTax(float $taxRate, Price $taxAmount): self
    {
        return new self(
            $this->originalPrice,
            $this->finalPrice,
            $this->appliedDiscounts,
            $this->customerType,
            $this->quantity,
            $this->metadata,
            $taxRate,
            $taxAmount
        );
    }

    /**
     * Dizi temsiline dönüştürür.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'original_price' => $this->originalPrice->toArray(),
            'final_price' => $this->finalPrice->toArray(),
            'unit_original_price' => $this->getUnitOriginalPrice()->toArray(),
            'unit_final_price' => $this->getUnitFinalPrice()->toArray(),
            'total_original_price' => $this->getTotalOriginalPrice()->toArray(),
            'total_final_price' => $this->getTotalFinalPrice()->toArray(),
            'discounts' => $this->appliedDiscounts->map(fn(Discount $discount) => $discount->toArray())->toArray(),
            'total_discount_amount' => $this->totalDiscountAmount->toArray(),
            'tax_rate' => $this->taxRate,
            'tax_amount' => $this->taxAmount->toArray(),
            'total_tax_amount' => $this->getTotalTaxAmount()->toArray(),
            'final_price_incl_tax' => $this->finalPriceWithTax->toArray(),
            'total_final_price_incl_tax' => $this->getTotalFinalPriceWithTax()->toArray(),
            'savings_percentage' => round($this->getSavingsPercentage(), 2),
            'customer_type' => $this->customerType->value,
            'quantity' => $this->quantity,
            'has_discounts' => $this->hasDiscounts(),
            'discount_count' => $this->getDiscountCount(),
            'metadata' => $this->metadata
        ];
    }

    /**
     * JSON temsili döndürür.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Metin temsili (runtime string'ler korunmuştur).
     */
    public function __toString(): string
    {
        $discountInfo = '';
        if ($this->hasDiscounts()) {
            $discountInfo = sprintf(' (Tasarruf %s - %.1f%%)', 
                $this->totalDiscountAmount->formatForDisplay(),
                $this->getSavingsPercentage()
            );
        }

        return sprintf(
            'Fiyat: %s → %s%s [%s, Adet: %d]',
            $this->originalPrice->formatForDisplay(),
            $this->finalPrice->formatForDisplay(),
            $discountInfo,
            $this->customerType->value,
            $this->quantity
        );
    }
}
