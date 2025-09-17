<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Services\Campaign\CampaignEngine;
use App\Services\Pricing\PriceEngine;
use App\ValueObjects\Campaign\CartContext;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Kampanya ve Fiyatlandırma sistemlerini entegre eden, sepet ve ürün bazlı
 * nihai fiyatları hesaplayan ana servis sınıfı.
 */
class CampaignPricingService
{
    /**
     * CampaignPricingService yapıcı metodu.
     *
     * @param PriceEngine $priceEngine Fiyat hesaplama motoru
     * @param CampaignEngine $campaignEngine Kampanya uygulama motoru
     */
    public function __construct(
        private readonly PriceEngine $priceEngine,
        private readonly CampaignEngine $campaignEngine
    ) {}

    /**
     * Bir alışveriş sepetindeki ürünler için fiyatları hesaplar ve uygun kampanyaları uygular.
     *
     * @param array $cartItems Sepetteki ürünleri içeren dizi. Her eleman ['variant' => ProductVariant, 'quantity' => int] formatında olmalıdır.
     * @param User|null $user İşlem yapan kullanıcı (varsa).
     * @param array $context Fiyatlandırma ve kampanyalar için ek bağlamsal veri.
     * @return array Fiyatlandırma sonuçları, kampanya öncesi toplam, uygulanan kampanyalar ve nihai toplamı içeren bir dizi.
     * @throws \App\Exceptions\Pricing\PricingException Fiyatlandırma sırasında bir hata oluşursa.
     */
    public function calculateCartPricing(array $cartItems, ?User $user = null, array $context = []): array
    {
        try {
            $pricingResults = new Collection();
            $totalAmount = 0;
            $customerType = $user ? 
                app(\App\Services\Pricing\CustomerTypeDetector::class)->detect($user)->value : 
                'guest';

            // Her cart item için fiyat hesapla
            $totalTaxAmount = 0;
            $totalAmountIncTax = 0;

            foreach ($cartItems as $item) {
                $variant = $item['variant'];
                $quantity = $item['quantity'];
                
                $priceResult = $this->priceEngine->calculatePrice($variant, $quantity, $user, $context);
                
                $pricingResults->push([
                    'item' => $item,
                    'pricing' => $priceResult,
                    'subtotal' => $priceResult->getFinalPrice()->getAmount() * $quantity,
                    'tax_amount' => $priceResult->getTotalTaxAmount()->getAmount(),
                    'total_with_tax' => $priceResult->getTotalFinalPriceWithTax()->getAmount()
                ]);
                
                $totalAmount += $priceResult->getFinalPrice()->getAmount() * $quantity;
                $totalTaxAmount += $priceResult->getTotalTaxAmount()->getAmount();
                $totalAmountIncTax += $priceResult->getTotalFinalPriceWithTax()->getAmount();
            }

            // CartContext oluştur
            $cartItemsArray = collect($cartItems)->map(function ($item) {
                return [
                    'product_id' => $item['variant']->product_id,
                    'variant_id' => $item['variant']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['variant']->price,
                    'category_ids' => $item['variant']->product->categories->pluck('id')->toArray()
                ];
            })->toArray();

            $cartContext = CartContext::fromItems(
                items: $cartItemsArray,
                totalAmount: $totalAmount,
                customerType: $customerType,
                customerId: $user?->id,
                metadata: $context
            );

            // Kampanyaları uygula
            $appliedCampaigns = $this->campaignEngine->applyCampaigns($cartContext, $user);

            $cartTotals = [
                'pricing_results' => $pricingResults,
                'total_before_campaigns' => $totalAmount,
                'tax_total_before_campaigns' => $totalTaxAmount,
                'total_before_campaigns_incl_tax' => $totalAmountIncTax,
                'applied_campaigns' => $appliedCampaigns,
                'campaign_benefits' => $this->calculateCampaignBenefits($appliedCampaigns),
                'free_items' => $this->extractFreeItems($appliedCampaigns)
            ];

            $finalTotals = $this->calculateFinalTotals($totalAmount, $totalTaxAmount, $appliedCampaigns);

            return array_merge($cartTotals, $finalTotals);

        } catch (\Exception $e) {
            Log::error('Campaign pricing calculation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user?->id
            ]);

            throw new \App\Exceptions\Pricing\PricingException(
                'Sepet fiyatlandırması hesaplanırken hata oluştu: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * Tek bir ürün için fiyatlandırma ve kampanya kontrolü yapar.
     * `calculateCartPricing` metodunu tek ürünlük bir sepet ile çağırır.
     *
     * @param \App\Models\ProductVariant $variant Fiyatı hesaplanacak ürün varyantı.
     * @param int $quantity Ürün adedi.
     * @param User|null $user İşlem yapan kullanıcı (varsa).
     * @param array $context Fiyatlandırma ve kampanyalar için ek bağlamsal veri.
     * @return array Fiyatlandırma sonuçlarını içeren dizi.
     */
    public function calculateProductPricing(
        \App\Models\ProductVariant $variant,
        int $quantity,
        ?User $user = null,
        array $context = []
    ): array {
        $cartItems = [[
            'variant' => $variant,
            'quantity' => $quantity
        ]];

        return $this->calculateCartPricing($cartItems, $user, $context);
    }

    /**
     * Belirtilen kullanıcı ve müşteri tipi için mevcut ve uygun olan tüm kampanyaları listeler.
     *
     * @param User|null $user Kampanyaları görüntülenecek kullanıcı (varsa).
     * @return Collection Mevcut kampanyaların koleksiyonu.
     */
    public function getAvailableCampaigns(?User $user = null): Collection
    {
        $customerType = $user ? 
            app(\App\Services\Pricing\CustomerTypeDetector::class)->detect($user)->value : 
            'guest';

        return $this->campaignEngine->getAvailableCampaigns($customerType, $user);
    }

    /**
     * Belirli bir kampanyanın performans istatistiklerini getirir.
     *
     * @param \App\Models\Campaign $campaign İstatistikleri alınacak kampanya.
     * @return array Kampanya istatistiklerini içeren dizi (kullanım sayısı, toplam ciro vb.).
     */
    public function getCampaignStats(\App\Models\Campaign $campaign): array
    {
        return $this->campaignEngine->getCampaignStats($campaign);
    }

    /**
     * Uygulanan kampanyaların sağladığı toplam faydayı (indirim, ücretsiz ürün değeri vb.) hesaplar.
     *
     * @param Collection $appliedCampaigns Uygulanan kampanyaların sonuçlarını içeren koleksiyon.
     * @return array Toplam indirim, ücretsiz ürün değeri, toplam fayda ve kampanya açıklamalarını içeren bir dizi.
     */
    private function calculateCampaignBenefits(Collection $appliedCampaigns): array
    {
        $totalDiscount = 0;
        $totalFreeItemsValue = 0;
        $descriptions = [];

        foreach ($appliedCampaigns as $campaignData) {
            $result = $campaignData['result'];
            
            $totalDiscount += $result->getDiscountAmount();
            
            $freeItemsValue = $result->getFreeItems()->sum(function ($item) {
                return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
            });
            $totalFreeItemsValue += $freeItemsValue;
            
            if ($result->getDescription()) {
                $descriptions[] = $result->getDescription();
            }
        }

        return [
            'total_discount' => $totalDiscount,
            'total_free_items_value' => $totalFreeItemsValue,
            'total_benefit' => $totalDiscount + $totalFreeItemsValue,
            'descriptions' => $descriptions
        ];
    }

    /**
     * Kampanya indirimlerini orijinal toplam tutardan düşerek nihai toplamı hesaplar.
     *
     * @param float $originalTotal Kampanyalar uygulanmadan önceki sepet toplamı.
     * @param Collection $appliedCampaigns Uygulanan kampanyaların koleksiyonu.
     * @return float İndirimler sonrası nihai sepet tutarı.
     */
    private function calculateFinalTotals(float $originalTotalExclTax, float $originalTaxTotal, Collection $appliedCampaigns): array
    {
        $discount = 0;
        $taxDiscount = 0;

        foreach ($appliedCampaigns as $campaignData) {
            $result = $campaignData['result'];
            $discount += $result->getDiscountAmount();

            $metadata = $result->getMetadata();
            if (isset($metadata['tax_discount_amount'])) {
                $taxDiscount += (float) $metadata['tax_discount_amount'];
            }
        }

        $finalExclTax = max(0, $originalTotalExclTax - $discount);
        $finalTaxTotal = max(0, $originalTaxTotal - $taxDiscount);
        $finalInclTax = $finalExclTax + $finalTaxTotal;

        return [
            'final_total_excl_tax' => $finalExclTax,
            'final_tax_total' => $finalTaxTotal,
            'final_total_incl_tax' => $finalInclTax,
        ];
    }

    /**
     * Uygulanan kampanyalardan gelen tüm ücretsiz ürünleri tek bir koleksiyonda birleştirir.
     *
     * @param Collection $appliedCampaigns Uygulanan kampanyaların koleksiyonu.
     * @return Collection Tüm ücretsiz ürünleri içeren koleksiyon.
     */
    private function extractFreeItems(Collection $appliedCampaigns): Collection
    {
        $freeItems = new Collection();

        foreach ($appliedCampaigns as $campaignData) {
            $result = $campaignData['result'];
            $freeItems = $freeItems->merge($result->getFreeItems());
        }

        return $freeItems;
    }
}
