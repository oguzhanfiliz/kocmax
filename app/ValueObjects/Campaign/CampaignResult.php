<?php

declare(strict_types=1);

namespace App\ValueObjects\Campaign;

use Illuminate\Support\Collection;

/**
 * Kampanya sonucu değer nesnesi.
 *
 * Bir kampanyanın uygulanıp uygulanmadığını, uygulandıysa indirim tutarı,
 * hediye ürünler, açıklama ve ek metaverileri tutar.
 */
class CampaignResult
{
    /**
     * Yapıcı metot.
     *
     * @param bool $applied Kampanya uygulandı mı?
     * @param Collection $freeItems Hediye ürünler koleksiyonu
     * @param float $discountAmount İndirim tutarı
     * @param string $description Kampanya açıklaması
     * @param array $metadata Ek metaveriler
     */
    public function __construct(
        private readonly bool $applied,
        private readonly Collection $freeItems = new Collection(),
        private readonly float $discountAmount = 0.0,
        private readonly string $description = '',
        private readonly array $metadata = []
    ) {}

    /**
     * Kampanyanın uygulanıp uygulanmadığını döndürür.
     */
    public function isApplied(): bool
    {
        return $this->applied;
    }

    /**
     * Hediye ürünleri döndürür.
     *
     * @return Collection<array>
     */
    public function getFreeItems(): Collection
    {
        return $this->freeItems;
    }

    /**
     * İndirim tutarını döndürür.
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    /**
     * Kampanya açıklamasını döndürür.
     */
    public function getDescription(): string
    {
        return $this->description;
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
     * Hediye ürün var mı?
     */
    public function hasFreeItems(): bool
    {
        return $this->freeItems->isNotEmpty();
    }

    /**
     * İndirim uygulandı mı?
     */
    public function hasDiscount(): bool
    {
        return $this->discountAmount > 0;
    }

    /**
     * Toplam faydayı (hediye ürün değeri + indirim) döndürür.
     */
    public function getTotalBenefit(): float
    {
        $freeItemsValue = $this->freeItems->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });

        return $freeItemsValue + $this->discountAmount;
    }

    /**
     * Dizi temsiline dönüştürür (loglama/hata ayıklama için).
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'applied' => $this->applied,
            'free_items' => $this->freeItems->toArray(),
            'discount_amount' => $this->discountAmount,
            'description' => $this->description,
            'total_benefit' => $this->getTotalBenefit(),
            'metadata' => $this->metadata
        ];
    }

    /**
     * Uygulanmayan kampanya sonucu üretir.
     *
     * @param string $reason Neden uygulanmadığı
     */
    public static function notApplied(string $reason = ''): self
    {
        return new self(
            applied: false,
            description: $reason
        );
    }

    /**
     * Hediye ürün(ler) içeren kampanya sonucu üretir.
     *
     * @param Collection $items Hediye ürünler
     * @param string $description Açıklama
     */
    public static function withFreeItems(Collection $items, string $description = ''): self
    {
        return new self(
            applied: true,
            freeItems: $items,
            description: $description
        );
    }

    /**
     * Yalnızca indirim içeren kampanya sonucu üretir.
     *
     * @param float $amount İndirim tutarı
     * @param string $description Açıklama
     */
    public static function withDiscount(float $amount, string $description = ''): self
    {
        return new self(
            applied: true,
            discountAmount: $amount,
            description: $description
        );
    }

    /**
     * Hem hediye ürün hem de indirim içeren kampanya sonucu üretir.
     *
     * @param Collection $freeItems Hediye ürünler
     * @param float $discountAmount İndirim tutarı
     * @param string $description Açıklama
     */
    public static function withBoth(Collection $freeItems, float $discountAmount, string $description = ''): self
    {
        return new self(
            applied: true,
            freeItems: $freeItems,
            discountAmount: $discountAmount,
            description: $description
        );
    }
}