<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Enums\Pricing\CustomerType;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\Discount;
use App\ValueObjects\Pricing\Price;
use Illuminate\Support\Collection;

class B2CPricingStrategy extends AbstractPricingStrategy
{
    /**
     * B2C müşterileri için fiyatlandırma stratejisi.
     */
    public function __construct()
    {
        parent::__construct(CustomerType::B2C, 80); // Yüksek öncelik, B2B'den düşük
    }

    /**
     * Varyantın temel fiyatını TRY cinsinden döndürür.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @return Price TRY cinsinden temel fiyat
     */
    public function getBasePrice(ProductVariant $variant): Price
    {
        // B2C müşterileri için fiyat TRY'ye çevrilerek hesaplanır
        try {
            $amountTry = $variant->getPriceInCurrency('TRY');
            if ($amountTry <= 0 && $variant->product?->base_price) {
                // Varyant fiyatı yoksa ürün baz fiyatını TRY'ye çevir
                $converter = app(\App\Services\CurrencyConversionService::class);
                $amountTry = $converter->convertPrice(
                    (float) $variant->product->base_price,
                    (string) ($variant->product->base_currency ?? 'TRY'),
                    'TRY'
                );
            }
        } catch (\Throwable $e) {
            // Son çare: ham değerler
            $amountTry = (float) ($variant->price ?? $variant->product->base_price ?? 0);
        }

        return new Price((float) $amountTry, 'TRY');
    }

    /**
     * B2C müşterileri için uygulanabilir indirimleri döndürür.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     * @param int $quantity Adet
     * @return Collection İndirimler koleksiyonu
     */
    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null,
        int $quantity = 1
    ): Collection {
        $discounts = collect();

        // Akıllı Fiyatlandırma (ProductListResource ile tutarlı indirim yüzdesi)
        try {
            /** @var \App\Services\Pricing\CustomerTypeDetectorService $detector */
            $detector = app(\App\Services\Pricing\CustomerTypeDetectorService::class);
            $smartPercentage = (float) $detector->getDiscountPercentage($customer, $quantity);
            if ($smartPercentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $smartPercentage,
                        'Akıllı Fiyatlandırma',
                        'Kullanıcı tipine göre otomatik indirim',
                        92 // Kampanyadan biraz düşük, B2C için yüksek öncelik
                    )
                );
            }
        } catch (\Throwable $e) {
            // Servis yoksa akıllı fiyatlandırmayı yok say
        }

        // Müşteri-özel indirimleri ekle
        $discounts = $discounts->merge($this->getCustomerDiscounts($variant, $customer, $quantity));

        // Promosyon indirimlerini ekle
        $discounts = $discounts->merge($this->getPromotionalDiscounts($variant, $quantity));

        // B2C için sınırlı toplu indirimler (daha küçük adetler)
        $discounts = $discounts->merge($this->getB2CBulkDiscounts($variant, $quantity));

        // Sezonsal/kampanya indirimlerini ekle
        $discounts = $discounts->merge($this->getSeasonalDiscounts($variant));

        // İlk alışveriş indirimini ekle
        $discounts = $discounts->merge($this->getFirstTimeCustomerDiscount($variant, $customer));

        return $discounts;
    }

    /**
     * B2C müşterisi için müşteri-özel indirimleri döndürür.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     * @param int $quantity Adet
     * @return Collection Müşteri-özel indirimler
     */
    protected function getCustomerDiscounts(ProductVariant $variant, ?User $customer = null, int $quantity = 1): Collection
    {
        $discounts = collect();

        if (!$customer) {
            return $discounts;
        }

        // Müşteri sadakat programı
        $totalOrders = $customer->orders()->completed()->count();
        
        if ($totalOrders >= 5) {
            $loyaltyPercentage = match(true) {
                $totalOrders >= 50 => 5.0,  // VIP Müşteri
                $totalOrders >= 25 => 3.0,  // Altın Müşteri
                $totalOrders >= 10 => 2.0,  // Gümüş Müşteri
                $totalOrders >= 5 => 1.0,   // Bronz Müşteri
                default => 0.0
            };

            if ($loyaltyPercentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $loyaltyPercentage,
                        'Müşteri Sadakat İndirimi',
                        "Sadakatiniz için teşekkürler ({$totalOrders} sipariş)",
                        70
                    )
                );
            }
        }

        // Doğum günü indirimi (doğum tarihi varsa)
        if ($customer->birth_date && $customer->birth_date->isCurrentMonth()) {
            $discounts->push(
                Discount::percentage(
                    5.0,
                    'Doğum Günü Fırsatı',
                    'Doğum gününüz kutlu olsun! Bu özel indirimin tadını çıkarın',
                    80
                )
            );
        }

        return $discounts;
    }

    /**
     * Aktif kampanyalara bağlı promosyon indirimlerini döndürür.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @return Collection Promosyon indirimleri
     */
    protected function getPromotionalDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Aktif kampanyaları kontrol et
        $campaigns = \App\Models\Campaign::active()
            ->whereHas('products', function($query) use ($variant) {
                $query->where('product_id', $variant->product_id);
            })
            ->get();

        foreach ($campaigns as $campaign) {
            if ($campaign->discount_percentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $campaign->discount_percentage,
                        $campaign->name,
                        $campaign->description ?? 'Özel promosyon teklifi',
                        90 // Aktif kampanyalar için yüksek öncelik
                    )
                );
            }
        }

        return $discounts;
    }

    /**
     * B2C için daha küçük adet eşikleriyle toplu alım indirimlerini döndürür.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @return Collection Uygulanabilir en yüksek tek indirim
     */
    protected function getB2CBulkDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // B2C müşterileri için daha küçük adet eşikleri
        $bulkTiers = [
            ['min_qty' => 3, 'discount' => 2.0, 'name' => '3+ Al %2 Kazan'],
            ['min_qty' => 5, 'discount' => 5.0, 'name' => '5+ Al %5 Kazan'],
            ['min_qty' => 10, 'discount' => 8.0, 'name' => '10+ Al %8 Kazan'],
            ['min_qty' => 20, 'discount' => 12.0, 'name' => '20+ Al %12 Kazan'],
        ];

        foreach ($bulkTiers as $tier) {
            if ($quantity >= $tier['min_qty']) {
                $discounts->push(
                    Discount::percentage(
                        $tier['discount'],
                        $tier['name'],
                        "{$tier['min_qty']}+ ürün için çoklu alım indirimi",
                        60
                    )
                );
            }
        }

        // Yalnızca en yüksek uygulanabilir indirimi döndür
        return $discounts->sortByDesc('value')->take(1);
    }

    /**
     * Sezona göre promosyonları değerlendirerek sezonsal indirimleri döndürür.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @return Collection Sezonsal indirimler
     */
    protected function getSeasonalDiscounts(ProductVariant $variant): Collection
    {
        $discounts = collect();
        
        $currentMonth = now()->month;
        $currentSeason = $this->getCurrentSeason($currentMonth);

        // Ürün kategorileri sezonsal promosyonlarla eşleşiyor mu kontrol et
        $productCategories = $variant->product->categories->pluck('name')->map(fn($name) => strtolower($name));

        $seasonalPromotions = [
            'winter' => [
                'categories' => ['coat', 'jacket', 'winter', 'thermal'],
                'discount' => 10.0,
                'name' => 'Winter Collection Sale'
            ],
            'summer' => [
                'categories' => ['summer', 't-shirt', 'shorts', 'sandal'],
                'discount' => 15.0,
                'name' => 'Summer Clearance'
            ],
            'spring' => [
                'categories' => ['spring', 'light jacket', 'sweater'],
                'discount' => 12.0,
                'name' => 'Spring Fashion'
            ],
            'autumn' => [
                'categories' => ['autumn', 'fall', 'boot', 'sweater'],
                'discount' => 8.0,
                'name' => 'Autumn Collection'
            ]
        ];

        if (isset($seasonalPromotions[$currentSeason])) {
            $promotion = $seasonalPromotions[$currentSeason];
            
            foreach ($promotion['categories'] as $category) {
                if ($productCategories->contains(fn($cat) => str_contains($cat, $category))) {
                    $discounts->push(
                        Discount::percentage(
                            $promotion['discount'],
                            $promotion['name'],
                            "{$currentSeason} koleksiyonu için sezonsal indirim",
                            40
                        )
                    );
                    break; // Yalnızca bir sezonsal indirim uygula
                }
            }
        }

        return $discounts;
    }

    /**
     * İlk alışveriş yapan müşteriler için indirimleri döndürür.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     * @return Collection İlk alışveriş indirimi
     */
    protected function getFirstTimeCustomerDiscount(ProductVariant $variant, ?User $customer = null): Collection
    {
        $discounts = collect();

        if (!$customer) {
            return $discounts;
        }

        // Müşterinin ilk siparişi mi kontrol et
        $orderCount = $customer->orders()->count();
        
        if ($orderCount === 0) {
            $discounts->push(
                Discount::percentage(
                    10.0,
                    'İlk Alışveriş',
                    'Hoş geldiniz! İlk siparişinizde %10 indirim',
                    75 // İlk alışveriş için yüksek öncelik
                )
            );
        }

        return $discounts;
    }

    /**
     * Ay bilgisine göre sezon adını döndürür.
     *
     * @param int $month Ay
     * @return string Sezon anahtarı (winter|spring|summer|autumn)
     */
    private function getCurrentSeason(int $month): string
    {
        return match(true) {
            in_array($month, [12, 1, 2]) => 'winter',
            in_array($month, [3, 4, 5]) => 'spring',
            in_array($month, [6, 7, 8]) => 'summer',
            in_array($month, [9, 10, 11]) => 'autumn',
            default => 'spring'
        };
    }

    /**
     * Bu strateji belirtilen müşteri tipini destekliyor mu?
     *
     * @param CustomerType $customerType Müşteri tipi
     * @return bool Destekliyorsa true
     */
    public function supports(CustomerType $customerType): bool
    {
        return $customerType->isB2C() && $customerType !== CustomerType::GUEST;
    }
}
