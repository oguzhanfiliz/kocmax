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

class FlashSaleHandler implements CampaignHandlerInterface
{
    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::FLASH_SALE->value;
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

            // Calculate flash sale discount
            $discountAmount = $this->calculateFlashDiscount($campaign, $context);
            
            if ($discountAmount <= 0) {
                return CampaignResult::failed('No discount applicable');
            }

            Log::info('Flash sale applied', [
                'campaign_id' => $campaign->id,
                'customer_id' => $context->getCustomerId(),
                'discount_amount' => $discountAmount,
                'cart_total' => $context->getTotalAmount()
            ]);

            return CampaignResult::discount(
                new Discount($discountAmount, 'Flash Sale: ' . $campaign->name),
                "Flaş indirim uygulandı: {$campaign->name}"
            );

        } catch (\Exception $e) {
            Log::error('Flash sale handler failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return CampaignResult::failed('Flash sale calculation failed');
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

        // Must have valid flash sale rules
        $rules = $campaign->rules ?? [];
        if (empty($rules['flash_discount_type']) || empty($rules['flash_discount_value'])) {
            return false;
        }

        return true;
    }

    private function validateContext(CartContext $context): bool
    {
        return $context->getItems()->isNotEmpty() && $context->getTotalAmount() > 0;
    }

    private function calculateFlashDiscount(Campaign $campaign, CartContext $context): float
    {
        $rules = $campaign->rules ?? [];
        $rewards = $campaign->rewards ?? [];
        
        $discountType = $rules['flash_discount_type'] ?? 'percentage';
        $discountValue = $rules['flash_discount_value'] ?? 0;
        $targetProducts = $rules['flash_sale_products'] ?? []; // Specific products or empty for all

        $cartItems = $context->getItems();
        $applicableAmount = 0;

        // Calculate total amount for applicable products
        foreach ($cartItems as $item) {
            $isApplicable = empty($targetProducts) || in_array($item['product_id'], $targetProducts);
            
            if ($isApplicable) {
                $applicableAmount += $item['price'] * $item['quantity'];
            }
        }

        if ($applicableAmount <= 0) {
            return 0;
        }

        // Calculate discount
        $discountAmount = match ($discountType) {
            'percentage' => ($applicableAmount * $discountValue) / 100,
            'fixed' => min($discountValue, $applicableAmount), // Don't exceed applicable amount
            default => 0
        };

        // Apply maximum discount limit
        $maxDiscount = $rewards['max_discount'] ?? null;
        if ($maxDiscount && $discountAmount > $maxDiscount) {
            $discountAmount = $maxDiscount;
        }

        // Limit to cart total
        $cartTotal = $context->getTotalAmount();
        return min($discountAmount, $cartTotal);
    }
}