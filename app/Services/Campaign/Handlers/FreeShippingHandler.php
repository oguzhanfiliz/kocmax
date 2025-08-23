<?php

declare(strict_types=1);

namespace App\Services\Campaign\Handlers;

use App\Contracts\Campaign\CampaignHandlerInterface;
use App\Enums\Campaign\CampaignType;
use App\Models\Campaign;
use App\ValueObjects\Campaign\CampaignResult;
use App\ValueObjects\Campaign\CartContext;
use App\ValueObjects\Pricing\Discount;
use Illuminate\Support\Facades\Log;

class FreeShippingHandler implements CampaignHandlerInterface
{
    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::FREE_SHIPPING->value;
    }

    public function apply(Campaign $campaign, CartContext $context): CampaignResult
    {
        try {
            // Validation chain
            if (!$this->validateCampaign($campaign)) {
                return CampaignResult::failed('Campaign validation failed');
            }

            if (!$this->validateContext($context)) {
                return CampaignResult::failed('Invalid cart context');
            }

            // Check if free shipping is applicable
            $shippingBenefit = $this->calculateShippingBenefit($campaign, $context);
            
            if (!$shippingBenefit['applicable']) {
                return CampaignResult::failed($shippingBenefit['reason']);
            }

            Log::info('Free shipping applied', [
                'campaign_id' => $campaign->id,
                'customer_id' => $context->getCustomerId(),
                'shipping_cost_saved' => $shippingBenefit['shipping_cost'],
                'trigger_reason' => $shippingBenefit['trigger_reason']
            ]);

            // Return as discount (shipping cost savings)
            return CampaignResult::discount(
                new Discount($shippingBenefit['shipping_cost'], 'Free Shipping: ' . $campaign->name),
                "Ücretsiz kargo uygulandı: {$campaign->name}"
            );

        } catch (\Exception $e) {
            Log::error('Free shipping handler failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return CampaignResult::failed('Free shipping calculation failed');
        }
    }

    private function validateCampaign(Campaign $campaign): bool
    {
        // Campaign must be active
        if (!$campaign->is_active) {
            return false;
        }

        // Campaign must be within date range
        $now = now();
        if ($campaign->starts_at && $now->lt($campaign->starts_at)) {
            return false;
        }

        if ($campaign->ends_at && $now->gt($campaign->ends_at)) {
            return false;
        }

        return true;
    }

    private function validateContext(CartContext $context): bool
    {
        return $context->getItems()->isNotEmpty() && $context->getTotalAmount() > 0;
    }

    private function calculateShippingBenefit(Campaign $campaign, CartContext $context): array
    {
        // Campaign type-specific fields kullan
        $minAmount = $campaign->free_shipping_min_amount ?? 0;
        $specialProducts = $campaign->products->pluck('id')->toArray();
        $standardShippingCost = config('shipping.default_cost', 25); // Configurable shipping cost

        $cartTotal = $context->getTotalAmount();
        $cartItems = $context->getItems();

        // Check if minimum amount is met
        if ($minAmount > 0 && $cartTotal >= $minAmount) {
            return [
                'applicable' => true,
                'shipping_cost' => $standardShippingCost,
                'trigger_reason' => "Minimum amount of {$minAmount}₺ reached"
            ];
        }

        // Check if cart contains special products that qualify for free shipping
        if (!empty($specialProducts)) {
            foreach ($cartItems as $item) {
                if (in_array($item['product_id'], $specialProducts)) {
                    return [
                        'applicable' => true,
                        'shipping_cost' => $standardShippingCost,
                        'trigger_reason' => 'Cart contains products with free shipping'
                    ];
                }
            }
        }

        // Check if all products have free shipping
        if (empty($specialProducts) && $minAmount <= 0) {
            return [
                'applicable' => true,
                'shipping_cost' => $standardShippingCost,
                'trigger_reason' => 'Universal free shipping campaign'
            ];
        }

        return [
            'applicable' => false,
            'reason' => $minAmount > 0 
                ? "Minimum cart amount of {$minAmount}₺ not reached (current: {$cartTotal}₺)"
                : 'No qualifying products for free shipping'
        ];
    }
}