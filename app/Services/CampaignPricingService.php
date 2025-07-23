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
 * Campaign ve Pricing sistemlerini birleştiren ana service
 */
class CampaignPricingService
{
    public function __construct(
        private readonly PriceEngine $priceEngine,
        private readonly CampaignEngine $campaignEngine
    ) {}

    /**
     * Sepet için fiyat hesaplama + kampanya uygulaması
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
            foreach ($cartItems as $item) {
                $variant = $item['variant'];
                $quantity = $item['quantity'];
                
                $priceResult = $this->priceEngine->calculatePrice($variant, $quantity, $user, $context);
                
                $pricingResults->push([
                    'item' => $item,
                    'pricing' => $priceResult,
                    'subtotal' => $priceResult->getFinalPrice()->getAmount() * $quantity
                ]);
                
                $totalAmount += $priceResult->getFinalPrice()->getAmount() * $quantity;
            }

            // CartContext oluştur
            $cartContext = new CartContext(
                items: collect($cartItems)->map(function ($item) {
                    return [
                        'product_id' => $item['variant']->product_id,
                        'variant_id' => $item['variant']->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['variant']->price,
                        'category_ids' => $item['variant']->product->categories->pluck('id')->toArray()
                    ];
                }),
                totalAmount: $totalAmount,
                customerType: $customerType,
                metadata: $context
            );

            // Kampanyaları uygula
            $appliedCampaigns = $this->campaignEngine->applyCampaigns($cartContext, $user);

            return [
                'pricing_results' => $pricingResults,
                'total_before_campaigns' => $totalAmount,
                'applied_campaigns' => $appliedCampaigns,
                'campaign_benefits' => $this->calculateCampaignBenefits($appliedCampaigns),
                'final_total' => $this->calculateFinalTotal($totalAmount, $appliedCampaigns),
                'free_items' => $this->extractFreeItems($appliedCampaigns)
            ];

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
     * Tek bir ürün için fiyat + kampanya kontrolü
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
     * Müşteri için mevcut kampanyaları getir
     */
    public function getAvailableCampaigns(?User $user = null): Collection
    {
        $customerType = $user ? 
            app(\App\Services\Pricing\CustomerTypeDetector::class)->detect($user)->value : 
            'guest';

        return $this->campaignEngine->getAvailableCampaigns($customerType, $user);
    }

    /**
     * Kampanya istatistiklerini getir
     */
    public function getCampaignStats(\App\Models\Campaign $campaign): array
    {
        return $this->campaignEngine->getCampaignStats($campaign);
    }

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

    private function calculateFinalTotal(float $originalTotal, Collection $appliedCampaigns): float
    {
        $discount = 0;

        foreach ($appliedCampaigns as $campaignData) {
            $discount += $campaignData['result']->getDiscountAmount();
        }

        return max(0, $originalTotal - $discount);
    }

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