<?php

declare(strict_types=1);

namespace App\Services\Campaign\Handlers;

use App\Contracts\Campaign\CampaignHandlerInterface;
use App\Models\Campaign;
use App\Models\User;
use App\ValueObjects\Campaign\CampaignResult;
use App\ValueObjects\Campaign\CartContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BuyXGetYFreeHandler implements CampaignHandlerInterface
{
    public function canApply(Campaign $campaign, CartContext $context, ?User $user = null): bool
    {
        try {
            // Kampanya aktif mi kontrol et
            if (!$campaign->isActive()) {
                return false;
            }

            // Kullanım limiti kontrol et
            if ($campaign->hasReachedUsageLimit()) {
                return false;
            }

            // Kullanıcı bazlı limit kontrol et
            if ($user && !$campaign->canBeUsedBy($user)) {
                return false;
            }

            // Müşteri tipi kontrol et
            if (!$campaign->isApplicableForCustomerType($context->getCustomerType())) {
                return false;
            }

            // Minimum sepet tutarı kontrol et
            if ($campaign->minimum_cart_amount && $context->getTotalAmount() < $campaign->minimum_cart_amount) {
                return false;
            }

            // Kampanya kurallarını kontrol et
            return $this->checkCampaignRules($campaign, $context);

        } catch (\Exception $e) {
            Log::error('BuyXGetYFree campaign check failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'context' => $context->toArray() ?? []
            ]);
            return false;
        }
    }

    public function apply(Campaign $campaign, CartContext $context, ?User $user = null): CampaignResult
    {
        try {
            if (!$this->canApply($campaign, $context, $user)) {
                return CampaignResult::notApplied('Kampanya koşulları sağlanmıyor');
            }

            $rules = $campaign->rules;
            $rewards = $campaign->rewards;

            // "3 Al 1 Hediye" logic'i
            $requiredQuantity = $rules['buy_quantity'] ?? 3;
            $freeQuantity = $rules['free_quantity'] ?? 1;
            $triggerProducts = $rules['trigger_products'] ?? [];
            $rewardProducts = $rewards['free_products'] ?? [];

            $freeItems = new Collection();

            // Trigger product'lar için kontrol
            if (!empty($triggerProducts)) {
                foreach ($triggerProducts as $productId) {
                    $cartQuantity = $context->getTotalQuantityForProduct($productId);
                    
                    // Kaç defa hediye hak edildi hesapla
                    $eligibleTimes = intval($cartQuantity / $requiredQuantity);
                    
                    if ($eligibleTimes > 0) {
                        // Hediye ürünlerini ekle
                        foreach ($rewardProducts as $rewardProductData) {
                            $rewardProductId = $rewardProductData['product_id'];
                            $rewardQuantity = ($rewardProductData['quantity'] ?? $freeQuantity) * $eligibleTimes;
                            
                            $freeItems->push([
                                'product_id' => $rewardProductId,
                                'variant_id' => $rewardProductData['variant_id'] ?? null,
                                'quantity' => $rewardQuantity,
                                'price' => $rewardProductData['price'] ?? 0,
                                'name' => $rewardProductData['name'] ?? "Hediye Ürün",
                                'campaign_id' => $campaign->id,
                                'campaign_type' => 'buy_x_get_y_free'
                            ]);
                        }
                    }
                }
            } else {
                // Genel "3 Al 1 Hediye" (herhangi bir üründen)
                $totalCartQuantity = $context->getTotalQuantity();
                $eligibleTimes = intval($totalCartQuantity / $requiredQuantity);
                
                if ($eligibleTimes > 0) {
                    foreach ($rewardProducts as $rewardProductData) {
                        $rewardQuantity = ($rewardProductData['quantity'] ?? $freeQuantity) * $eligibleTimes;
                        
                        $freeItems->push([
                            'product_id' => $rewardProductData['product_id'],
                            'variant_id' => $rewardProductData['variant_id'] ?? null,
                            'quantity' => $rewardQuantity,
                            'price' => $rewardProductData['price'] ?? 0,
                            'name' => $rewardProductData['name'] ?? "Hediye Ürün",
                            'campaign_id' => $campaign->id,
                            'campaign_type' => 'buy_x_get_y_free'
                        ]);
                    }
                }
            }

            if ($freeItems->isNotEmpty()) {
                $description = sprintf(
                    "%d Al %d Hediye kampanyasından %d adet hediye ürün",
                    $requiredQuantity,
                    $freeQuantity,
                    $freeItems->sum('quantity')
                );

                return CampaignResult::withFreeItems($freeItems, $description);
            }

            return CampaignResult::notApplied('Yeterli ürün adedi yok');

        } catch (\Exception $e) {
            Log::error('BuyXGetYFree campaign application failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return CampaignResult::notApplied('Kampanya uygulanırken hata oluştu');
        }
    }

    public function getSupportedType(): string
    {
        return 'buy_x_get_y_free';
    }

    public function getPriority(): int
    {
        return 80; // Yüksek öncelik
    }

    private function checkCampaignRules(Campaign $campaign, CartContext $context): bool
    {
        $rules = $campaign->rules;
        
        if (empty($rules)) {
            return false;
        }

        $requiredQuantity = $rules['buy_quantity'] ?? 3;
        $triggerProducts = $rules['trigger_products'] ?? [];

        // Belirli ürünler için kontrol
        if (!empty($triggerProducts)) {
            foreach ($triggerProducts as $productId) {
                $cartQuantity = $context->getTotalQuantityForProduct($productId);
                if ($cartQuantity >= $requiredQuantity) {
                    return true;
                }
            }
            return false;
        }

        // Genel kontrol (herhangi bir üründen)
        return $context->getTotalQuantity() >= $requiredQuantity;
    }
}