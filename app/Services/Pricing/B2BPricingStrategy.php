<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Enums\Pricing\CustomerType;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\Discount;
use App\ValueObjects\Pricing\Price;
use Illuminate\Support\Collection;

/**
 * B2B (Bayi) müşterileri için fiyatlandırma stratejisi.
 *
 * Yüksek öncelikli bir stratejidir ve bayi/kurumsal müşterilere özel indirim
 * kurallarını uygular. Temel fiyat TRY cinsinden hesaplanır, müşteri tipi ve
 * iş kuralları doğrultusunda indirimler birleştirilir.
 */
class B2BPricingStrategy extends AbstractPricingStrategy
{
    /**
     * B2B fiyatlandırma stratejisi yapıcı metodu.
     *
     * En yüksek öncelik (100) ile başlatılır.
     */
    public function __construct()
    {
        parent::__construct(CustomerType::B2B, 100); // En yüksek öncelik
    }

    /**
     * Ürünün B2B temel fiyatını hesaplar ve varsayılan B2B indirimini uygular.
     *
     * @param ProductVariant $variant Fiyatı hesaplanacak ürün varyantı
     * @return Price TRY cinsinden temel fiyat
     */
    public function getBasePrice(ProductVariant $variant): Price
    {
        // B2B fiyatını TRY'ye çevir ve varsayılan B2B indirimi uygula
        try {
            $amountTry = $variant->getPriceInCurrency('TRY');
            if ($amountTry <= 0 && $variant->product?->base_price) {
                $converter = app(\App\Services\CurrencyConversionService::class);
                $amountTry = $converter->convertPrice(
                    (float) $variant->product->base_price,
                    (string) ($variant->product->base_currency ?? 'TRY'),
                    'TRY'
                );
            }
        } catch (\Throwable $e) {
            $amountTry = (float) ($variant->price ?? $variant->product->base_price ?? 0);
        }

        // Varsayılan B2B indirimi uygula
        $defaultDiscount = $this->customerType->getDefaultDiscountPercentage();
        if ($defaultDiscount > 0) {
            $amountTry = $amountTry * (1 - $defaultDiscount / 100);
        }

        return new Price((float) $amountTry, 'TRY');
    }

    /**
     * Ürün için uygulanabilir tüm indirimleri toplar ve birleştirir.
     *
     * @param ProductVariant $variant İndirim uygulanacak ürün varyantı
     * @param User|null $customer Müşteri bilgisi (opsiyonel)
     * @param int $quantity Sipariş adedi
     * @return Collection İndirimlerin birleşik listesi
     */
    public function getAvailableDiscounts(
        ProductVariant $variant,  // İndirim uygulanacak ürün varyantı
        ?User $customer = null,   // Müşteri bilgisi (opsiyonel)
        int $quantity = 1         // Sipariş adedi
    ): Collection {
        $discounts = collect();

        // Akıllı Fiyatlandırma (Müşteri tipine göre otomatik indirim)
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
                        92 // Bayi indirimlerinin altında, yine de yüksek öncelik
                    )
                );
            }
        } catch (\Throwable $e) {
            // Servis kullanılamıyorsa akıllı fiyatlandırmayı atla
        }

        // Müşteri-özel bayi indirimlerini ekle
        $discounts = $discounts->merge($this->getCustomerDiscounts($variant, $customer, $quantity));

        // Toplu alım indirimlerini ekle
        $discounts = $discounts->merge($this->getBulkDiscounts($variant, $quantity));

        // Kategori bazlı indirimleri ekle
        $discounts = $discounts->merge($this->getCategoryDiscounts($variant, $customer));

        // Hacim bazlı kademeli indirimleri ekle
        $discounts = $discounts->merge($this->getVolumeDiscounts($variant, $quantity));

        // Uzun süreli B2B müşterileri için sadakat indirimlerini ekle
        $discounts = $discounts->merge($this->getLoyaltyDiscounts($variant, $customer));

        return $discounts;
    }

    /**
     * Müşteriye özel bayi indirimlerini getirir.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     * @param int $quantity Adet
     * @return Collection Bayi indirimleri
     */
    protected function getCustomerDiscounts(ProductVariant $variant, ?User $customer = null, int $quantity = 1): Collection
    {
        $discounts = collect();

        // Sadece bayi kullanıcılar için indirim uygula
        if (!$customer || !$customer->hasRole('dealer')) {
            return $discounts;
        }

        // Bu ürün için tanımlı bayi indirimlerini getir
        $dealerDiscounts = \App\Models\DealerDiscount::active()
            ->forDealer($customer->id)
            ->forProduct($variant->product_id)
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc')
            ->get();

        foreach ($dealerDiscounts as $dealerDiscount) {
            $discounts->push(
                $dealerDiscount->discount_type === 'percentage' 
                    ? Discount::percentage(
                        $dealerDiscount->discount_value,
                        'Dealer Discount',
                        "Exclusive dealer pricing for {$customer->name}",
                        95 // Çok yüksek öncelik
                    )
                    : Discount::fixedAmount(
                        $dealerDiscount->discount_value,
                        'Dealer Discount',
                        "Exclusive dealer pricing for {$customer->name}",
                        95
                    )
            );
        }

        return $discounts;
    }

    /**
     * Hacim bazlı indirimleri hesaplar (miktara göre artan indirimler).
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @return Collection Uygulanabilir en yüksek hacim indirimi (tek kayıt)
     */
    protected function getVolumeDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // B2B için hacim indirimi kademeleri
        $volumeTiers = [
            ['min_qty' => 100, 'discount' => 5.0, 'name' => 'Hacim İndirimi - 100+ Adet'],
            ['min_qty' => 500, 'discount' => 10.0, 'name' => 'Hacim İndirimi - 500+ Adet'],
            ['min_qty' => 1000, 'discount' => 15.0, 'name' => 'Hacim İndirimi - 1000+ Adet'],
            ['min_qty' => 5000, 'discount' => 20.0, 'name' => 'Hacim İndirimi - 5000+ Adet'],
        ];

        foreach ($volumeTiers as $tier) {
            if ($quantity >= $tier['min_qty']) {
                $discounts->push(
                    Discount::percentage(
                        $tier['discount'],
                        $tier['name'],
                        "Get {$tier['discount']}% off for {$tier['min_qty']}+ items",
                        85 // Yüksek öncelik ancak bayi indirimlerinden düşük
                    )
                );
            }
        }

        // Yalnızca en yüksek uygulanabilir indirimi döndür
        return $discounts->sortByDesc('value')->take(1);
    }

    /**
     * Uzun süreli müşteriler için sadakat indirimlerini hesaplar.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     * @return Collection Sadakat indirimleri
     */
    protected function getLoyaltyDiscounts(ProductVariant $variant, ?User $customer = null): Collection
    {
        $discounts = collect();

        // Sadece bayi kullanıcılar için sadakat indirimi uygula
        if (!$customer || !$customer->hasRole('dealer')) {
            return $discounts;
        }

        // Müşterinin sipariş geçmişini ve üyelik süresini kontrol et
        $customerSince = $customer->created_at;
        $monthsAsCustomer = $customerSince->diffInMonths(now());
        
        $totalOrders = $customer->orders()->completed()->count();
        $totalSpent = (float) ($customer->orders()->completed()->sum('total_amount') ?? 0);

        // İlişki süresine bağlı sadakat indirimi
        if ($monthsAsCustomer >= 12) {
            $loyaltyPercentage = match(true) {
                $monthsAsCustomer >= 60 => 3.0, // 5+ yıl
                $monthsAsCustomer >= 36 => 2.5, // 3+ yıl  
                $monthsAsCustomer >= 24 => 2.0, // 2+ yıl
                $monthsAsCustomer >= 12 => 1.0, // 1+ yıl
                default => 0.0
            };

            if ($loyaltyPercentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $loyaltyPercentage,
                        'Loyalty Discount',
                        "Thank you for being a customer for {$monthsAsCustomer} months",
                        50 // Daha düşük öncelik
                    )
                );
            }
        }

        // Yüksek değerli müşteri indirimi
        if ($totalSpent >= 50000) {
            $vipPercentage = match(true) {
                $totalSpent >= 500000 => 5.0, // VIP
                $totalSpent >= 250000 => 3.0, // Premium
                $totalSpent >= 100000 => 2.0, // Altın
                $totalSpent >= 50000 => 1.0,  // Gümüş
                default => 0.0
            };

            if ($vipPercentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $vipPercentage,
                        'VIP Customer Discount',
                        "Exclusive discount for high-value customers",
                        55 // Sadakatten biraz daha yüksek
                    )
                );
            }
        }

        return $discounts;
    }

    /**
     * Bu strateji belirtilen müşteri tipini destekliyor mu?
     *
     * @param CustomerType $customerType Müşteri tipi
     * @return bool Destekliyorsa true
     */
    public function supports(CustomerType $customerType): bool
    {
        return $customerType->isB2B();
    }
}
