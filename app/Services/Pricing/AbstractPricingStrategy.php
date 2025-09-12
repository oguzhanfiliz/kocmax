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

/**
 * Fiyatlandırma stratejileri için ortak davranışları barındıran soyut sınıf.
 *
 * - Temel fiyatın hesaplanması, indirimlerin uygulanması ve giriş doğrulamaları
 *   gibi ortak akışları içerir.
 * - Alt sınıflar müşteri tipine göre (B2B, B2C, misafir vb.) özel indirim kurallarını
 *   uygulamak için gerekli metotları sağlar.
 */
abstract class AbstractPricingStrategy implements PricingStrategyInterface
{
    // Müşteri tipini tutar (B2B, B2C, vb.)
    protected CustomerType $customerType;
    // Stratejinin öncelik değeri (yüksek değer daha yüksek öncelik anlamına gelir)
    protected int $priority;

    /**
     * Yapıcı: müşteri tipini ve strateji önceliğini ayarlar.
     *
     * @param CustomerType $customerType Müşteri tipi (B2B, B2C, vb.)
     * @param int $priority Öncelik değeri (yüksek değer daha yüksek öncelik)
     */
    public function __construct(CustomerType $customerType, int $priority = 0)
    {
        $this->customerType = $customerType;
        $this->priority = $priority;
    }

    /**
     * Ürün varyantı için nihai fiyatı hesaplar.
     *
     * @param ProductVariant $variant Fiyatı hesaplanacak ürün varyantı
     * @param int $quantity Sipariş adedi (>= 1)
     * @param User|null $customer Müşteri (opsiyonel)
     * @param array $context Ek bağlam bilgileri
     * @return PriceResult Fiyatlandırma sonucu (orijinal/nihai fiyat, uygulanan indirimler)
     */
    public function calculatePrice(
        ProductVariant $variant,  // Fiyatı hesaplanacak ürün varyantı
        int $quantity = 1,        // Sipariş adedi
        ?User $customer = null,   // Müşteri bilgisi (opsiyonel)
        array $context = []       // Ek bağlam bilgileri
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

    /**
     * Belirtilen müşteri tipinin bu strateji tarafından desteklenip desteklenmediğini kontrol eder.
     *
     * @param CustomerType $customerType Kontrol edilecek müşteri tipi
     * @return bool Destekleniyorsa true, aksi halde false
     */
    public function supports(CustomerType $customerType): bool
    {
        return $this->customerType === $customerType;
    }

    /**
     * Stratejinin bağlı olduğu müşteri tipini döndürür.
     *
     * @return CustomerType Müşteri tipi
     */
    public function getCustomerType(): CustomerType
    {
        return $this->customerType;
    }

    /**
     * Stratejinin öncelik değerini döndürür (yüksek değer daha yüksek öncelik).
     *
     * @return int Öncelik
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Belirtilen ürün ve miktar için fiyat hesaplanıp hesaplanamayacağını kontrol eder.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @param User|null $customer Müşteri (opsiyonel)
     * @return bool Hesaplanabiliyorsa true
     */
    public function canCalculatePrice(
        ProductVariant $variant,  // Kontrol edilecek ürün varyantı
        int $quantity,            // Sipariş adedi
        ?User $customer = null    // Müşteri bilgisi (opsiyonel)
    ): bool {
        try {
            $this->validateInputs($variant, $quantity);
            return $this->getBasePrice($variant)->getAmount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Giriş parametrelerini doğrular.
     * - Miktar > 0 olmalı
     * - Varyant aktif olmalı
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     */
    protected function validateInputs(ProductVariant $variant, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Miktar 0\'dan büyük olmalıdır');
        }

        if (!$variant->is_active) {
            throw new InvalidArgumentException('Aktif olmayan ürün varyantı için fiyat hesaplanamaz');
        }
    }

    /**
     * Temel fiyata indirimleri sırasıyla uygular ve nihai fiyatı döndürür.
     *
     * @param Price $basePrice Temel fiyat
     * @param Collection $discounts Uygulanabilir indirimler
     * @param int $quantity Adet
     * @return Price Nihai fiyat
     */
    protected function applyDiscounts(Price $basePrice, Collection $discounts, int $quantity): Price
    {
        $currentPrice = $basePrice;

        // İndirimleri önceliklerine göre sırala (yüksek öncelik önce)
        $sortedDiscounts = $discounts->sortByDesc(fn(Discount $discount) => $discount->getPriority());

        foreach ($sortedDiscounts as $discount) {
            if ($discount->canApplyTo($currentPrice, $quantity)) {
                $currentPrice = $discount->apply($currentPrice);
            }
        }

        return $currentPrice;
    }

    /**
     * Uygulanan indirimlerin listesini döndürür.
     *
     * @param Price $basePrice Temel fiyat
     * @param Collection $discounts İndirimler
     * @param int $quantity Adet
     * @return Collection Uygulanan indirim listesi
     */
    protected function getAppliedDiscounts(Price $basePrice, Collection $discounts, int $quantity): Collection
    {
        $appliedDiscounts = collect();
        $currentPrice = $basePrice;

        // İndirimleri önceliklerine göre sırala (yüksek öncelik önce)
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
     * Müşteriye özel indirimleri getirir (alt sınıflar tarafından uygulanır).
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     * @param int $quantity Adet
     * @return Collection İndirimler
     */
    abstract protected function getCustomerDiscounts(ProductVariant $variant, ?User $customer = null, int $quantity = 1): Collection;

    /**
     * Miktara dayalı toplu alım indirimlerini getirir.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @return Collection Toplu alım indirimleri
     */
    protected function getBulkDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Veritabanından toplu alım indirimlerini getir
        $bulkDiscounts = \App\Models\BulkDiscount::active()
            ->forProduct($variant->product_id)
            ->forQuantity($quantity)
            ->get();

        foreach ($bulkDiscounts as $bulkDiscount) {
            $discounts->push(
                Discount::percentage(
                    $bulkDiscount->discount_percentage,
                    'Toplu Alım İndirimi',
                    "{$quantity}+ adet için %{$bulkDiscount->discount_percentage} indirim",
                    100 // Toplu alımlar için yüksek öncelik
                )
            );
        }

        return $discounts;
    }

    /**
     * Kategori bazlı indirimleri getirir.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     * @return Collection Kategori indirimleri
     */
    protected function getCategoryDiscounts(ProductVariant $variant, ?User $customer = null): Collection
    {
        $discounts = collect();

        if (!$customer) {
            return $discounts;
        }

        // Bayi müşterileri için kategori indirimlerini getir
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
                            'Kategori İndirimi',
                            "Bayi kategori indirimi",
                            90 // Yüksek öncelik ama toplu alımdan düşük
                        )
                        : Discount::fixedAmount(
                            $categoryDiscount->discount_value,
                            'Kategori İndirimi',
                            "Bayi kategori indirimi",
                            90
                        )
                );
            }
        }

        return $discounts;
    }
}