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
 * Misafir (oturum açmamış) kullanıcılar için fiyatlandırma stratejisi.
 *
 * Temel fiyat TRY cinsinden hesaplanır ve oturum gerektirmeyen herkese açık
 * promosyonlar ile sınırlı toplu alım indirimlerini uygular.
 */
class GuestPricingStrategy extends AbstractPricingStrategy
{
    /**
     * Misafir (oturum açmamış) kullanıcılar için fiyatlandırma stratejisi.
     */
    public function __construct()
    {
        parent::__construct(CustomerType::GUEST, 60); // Daha düşük öncelik
    }

    /**
     * Varyantın temel fiyatını TRY cinsinden döndürür.
     *
     * Parametreler:
     * - variant: ProductVariant — fiyatı hesaplanacak ürün varyantı
     *
     * Döner:
     * - Price — TRY para biriminde temel fiyat
     */
    public function getBasePrice(ProductVariant $variant): Price
    {
        // Misafir kullanıcılar için fiyat TRY'ye çevrilerek hesaplanır
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

        return new Price((float) $amountTry, 'TRY');
    }

    /**
     * Misafir kullanıcılar için uygulanabilir indirimleri döndürür.
     *
     * Parametreler:
     * - variant: ProductVariant — indirimleri hesaplanacak ürün varyantı
     * - customer: ?User — kullanıcı (misafir olabilir)
     * - quantity: int — adet (varsayılan 1)
     *
     * Döner:
     * - Collection — uygulanabilir indirimlerin listesi
     */
    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null,
        int $quantity = 1
    ): Collection {
        $discounts = collect();

        // Misafir kullanıcılar için sınırlı indirimler
        $discounts = $discounts->merge($this->getPublicPromotions($variant, $quantity));
        $discounts = $discounts->merge($this->getMinimumBulkDiscounts($variant, $quantity));
        $discounts = $discounts->merge($this->getSignUpIncentiveDiscounts($variant));

        return $discounts;
    }

    /**
     * Misafire özel müşteri bazlı indirimleri döndürür (misafirlerde boş set).
     *
     * Parametreler:
     * - variant: ProductVariant — ürün varyantı
     * - customer: ?User — kullanıcı
     * - quantity: int — adet
     *
     * Döner:
     * - Collection — misafirler için boş koleksiyon
     */
    protected function getCustomerDiscounts(ProductVariant $variant, ?User $customer = null, int $quantity = 1): Collection
    {
        // Misafirlerin müşteri-özel indirimleri yoktur
        return collect();
    }

    /**
     * Oturum gerektirmeyen herkese açık kampanyalardan indirimleri döndürür.
     *
     * Parametreler:
     * - variant: ProductVariant — ürün varyantı
     * - quantity: int — adet
     *
     * Döner:
     * - Collection — herkese açık kampanyalara dayalı indirimler
     */
    protected function getPublicPromotions(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Giriş gerektirmeyen herkese açık kampanyalar
        $campaigns = \App\Models\Campaign::active()
            ->where('is_public', true) // Varsayımsal olarak herkese açık bayrağı
            ->whereHas('products', function($query) use ($variant) {
                $query->where('product_id', $variant->product_id);
            })
            ->get();

        foreach ($campaigns as $campaign) {
            if ($campaign->discount_percentage > 0 && $campaign->discount_percentage <= 15) { // Misafirler için indirim sınırı
                $discounts->push(
                    Discount::percentage(
                        $campaign->discount_percentage,
                        $campaign->name,
                        $campaign->description ?? 'Sınırlı süreli teklif',
                        70 // Herkese açık kampanyalar için iyi öncelik
                    )
                );
            }
        }

        return $discounts;
    }

    /**
     * Misafirler için çok sınırlı toplu alım indirimlerini döndürür.
     *
     * Parametreler:
     * - variant: ProductVariant — ürün varyantı
     * - quantity: int — adet
     *
     * Döner:
     * - Collection — uygun ise en yüksek tek indirim
     */
    protected function getMinimumBulkDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Misafirler için çok sınırlı toplu alım indirimleri
        $bulkTiers = [
            ['min_qty' => 5, 'discount' => 2.0, 'name' => 'Çoklu Ürün İndirimi'],
            ['min_qty' => 10, 'discount' => 5.0, 'name' => 'Toplu Alım İndirimi'],
        ];

        foreach ($bulkTiers as $tier) {
            if ($quantity >= $tier['min_qty']) {
                $discounts->push(
                    Discount::percentage(
                        $tier['discount'],
                        $tier['name'],
                        "{$tier['min_qty']}+ adet alımda tasarruf edin",
                        50
                    )
                );
            }
        }

        // Yalnızca en yüksek uygulanabilir indirimi döndür
        return $discounts->sortByDesc('value')->take(1);
    }

    /**
     * Misafirleri üye olmaya teşvik eden indirimleri döndürür.
     *
     * Parametreler:
     * - variant: ProductVariant — ürün varyantı
     *
     * Döner:
     * - Collection — üyelik teşvik indirimleri
     */
    protected function getSignUpIncentiveDiscounts(ProductVariant $variant): Collection
    {
        $discounts = collect();

        // Misafir kullanıcıları üye olmaya teşvik et
        $discounts->push(
            Discount::percentage(
                5.0,
                'Üye Ol ve Kazan',
                'Bu indirim ve daha fazla avantaj için hesap oluşturun',
                30 // Hemen uygulanabilir olmadığından daha düşük öncelik
            )
        );

        return $discounts;
    }

    /**
     * Verilen parametrelerle misafir için fiyat hesaplanabilir mi kontrol eder.
     *
     * Parametreler:
     * - variant: ProductVariant — ürün varyantı
     * - quantity: int — adet
     * - customer: ?User — kullanıcı (misafir olabilir)
     *
     * Döner:
     * - bool — hesaplanabilirse true, aksi halde false
     */
    public function canCalculatePrice(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool {
        // Misafirler fiyatları görebilir fakat sınırlamalar vardır
        try {
            $this->validateInputs($variant, $quantity);
            
            // Misafirlerde adet sınırlaması olabilir
            if ($quantity > 100) { // Misafirler için keyfi sınır
                return false;
            }
            
            return $this->getBasePrice($variant)->getAmount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Bu stratejinin belirtilen müşteri tipini destekleyip desteklemediğini döndürür.
     *
     * Parametreler:
     * - customerType: CustomerType — müşteri tipi
     *
     * Döner:
     * - bool — destekliyorsa true, aksi halde false
     */
    public function supports(CustomerType $customerType): bool
    {
        return $customerType === CustomerType::GUEST;
    }
}
