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

class QuantityDiscountHandler implements CampaignHandlerInterface
{
    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::QUANTITY_DISCOUNT->value;
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

            // Calculate quantity discount
            $discountResult = $this->calculateQuantityDiscount($campaign, $context);
            
            if ($discountResult['discount_amount'] <= 0) {
                return CampaignResult::failed($discountResult['reason'] ?? 'No discount applicable');
            }

            Log::info('Quantity discount applied', [
                'campaign_id' => $campaign->id,
                'customer_id' => $context->getCustomerId(),
                'qualifying_products' => $discountResult['qualifying_products'],
                'total_quantity' => $discountResult['total_quantity'],
                'discount_amount' => $discountResult['discount_amount'],
                'tier_applied' => $discountResult['tier_applied']
            ]);

            return CampaignResult::discount(
                new Discount($discountResult['discount_amount'], 'Quantity Discount: ' . $campaign->name),
                "Miktar indirimi uygulandı: {$campaign->name} ({$discountResult['tier_applied']['description']})"
            );

        } catch (\Exception $e) {
            Log::error('Quantity discount handler failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return CampaignResult::failed('Quantity discount calculation failed');
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

        // Must have valid quantity tiers
        $rules = $campaign->rules ?? [];
        if (empty($rules['quantity_tiers']) || !is_array($rules['quantity_tiers'])) {
            return false;
        }

        return true;
    }

    private function validateContext(CartContext $context): bool
    {
        return $context->getItems()->isNotEmpty() && $context->getTotalAmount() > 0;
    }

    private function calculateQuantityDiscount(Campaign $campaign, CartContext $context): array
    {
        $rules = $campaign->rules ?? [];
        $rewards = $campaign->rewards ?? [];
        
        $quantityTiers = $rules['quantity_tiers'] ?? [];
        $targetProducts = $rules['target_products'] ?? []; // Specific products or empty for all
        $targetCategories = $rules['target_categories'] ?? [];
        $applyToAll = $rules['apply_to_all'] ?? true; // All products or just qualifying ones
        
        // Get qualifying products and calculate total quantity
        $qualifyingResult = $this->getQualifyingProducts($context, $targetProducts, $targetCategories);
        
        if ($qualifyingResult['total_quantity'] <= 0) {
            return [
                'discount_amount' => 0,
                'reason' => 'Miktar indirimi için uygun ürün bulunamadı'
            ];
        }

        // Find applicable tier
        $applicableTier = $this->findApplicableTier($quantityTiers, $qualifyingResult['total_quantity']);
        
        if (!$applicableTier) {
            return [
                'discount_amount' => 0,
                'reason' => 'Minimum miktar şartı karşılanmadı'
            ];
        }

        // Calculate discount based on tier and application type
        $discountAmount = $this->calculateTierDiscount(
            $applicableTier, 
            $qualifyingResult, 
            $context, 
            $applyToAll
        );

        // Apply maximum discount limit
        $maxDiscount = $rewards['max_discount'] ?? null;
        if ($maxDiscount && $discountAmount > $maxDiscount) {
            $discountAmount = $maxDiscount;
        }

        // Limit to cart total
        $cartTotal = $context->getTotalAmount();
        $discountAmount = min($discountAmount, $cartTotal);

        return [
            'discount_amount' => $discountAmount,
            'qualifying_products' => $qualifyingResult['products'],
            'total_quantity' => $qualifyingResult['total_quantity'],
            'tier_applied' => $applicableTier,
            'apply_to_all' => $applyToAll
        ];
    }

    private function getQualifyingProducts(CartContext $context, array $targetProducts, array $targetCategories): array
    {
        $cartItems = $context->getItems();
        $qualifyingProducts = [];
        $totalQuantity = 0;

        foreach ($cartItems as $item) {
            $isQualifying = false;

            // Check if specific products are targeted
            if (!empty($targetProducts)) {
                $isQualifying = in_array($item['product_id'], $targetProducts);
            }
            // Check if specific categories are targeted
            elseif (!empty($targetCategories)) {
                $productCategories = $item['categories'] ?? [];
                $isQualifying = !empty(array_intersect($productCategories, $targetCategories));
            }
            // If no specific targeting, all products qualify
            else {
                $isQualifying = true;
            }

            if ($isQualifying) {
                $qualifyingProducts[] = $item;
                $totalQuantity += $item['quantity'];
            }
        }

        return [
            'products' => $qualifyingProducts,
            'total_quantity' => $totalQuantity
        ];
    }

    private function findApplicableTier(array $tiers, int $totalQuantity): ?array
    {
        // Sort tiers by minimum quantity in descending order to find the highest applicable tier
        usort($tiers, function ($a, $b) {
            return ($b['min_quantity'] ?? 0) <=> ($a['min_quantity'] ?? 0);
        });

        foreach ($tiers as $tier) {
            $minQuantity = $tier['min_quantity'] ?? 0;
            if ($totalQuantity >= $minQuantity) {
                return $tier;
            }
        }

        return null;
    }

    private function calculateTierDiscount(array $tier, array $qualifyingResult, CartContext $context, bool $applyToAll): float
    {
        $discountType = $tier['discount_type'] ?? 'percentage';
        $discountValue = $tier['discount_value'] ?? 0;
        $maxQuantity = $tier['max_quantity'] ?? null; // Maximum quantity to apply discount to
        
        // Determine which products to apply discount to
        $targetProducts = $applyToAll ? $context->getItems()->toArray() : $qualifyingResult['products'];
        
        $totalDiscount = 0;
        $processedQuantity = 0;

        foreach ($targetProducts as $product) {
            if ($maxQuantity && $processedQuantity >= $maxQuantity) {
                break;
            }

            $productQuantity = $product['quantity'];
            $productPrice = $product['price'];
            
            // Apply quantity limit if specified
            if ($maxQuantity) {
                $remainingLimit = $maxQuantity - $processedQuantity;
                $applicableQuantity = min($productQuantity, $remainingLimit);
            } else {
                $applicableQuantity = $productQuantity;
            }

            $productDiscount = match ($discountType) {
                'percentage' => ($productPrice * $applicableQuantity * $discountValue) / 100,
                'fixed' => $discountValue, // Fixed discount total (not per item)
                'fixed_per_item' => $discountValue * $applicableQuantity,
                'tiered_percentage' => $this->calculateTieredPercentageDiscount($tier, $applicableQuantity, $productPrice),
                default => 0
            };

            $totalDiscount += $productDiscount;
            $processedQuantity += $applicableQuantity;
        }

        return $totalDiscount;
    }

    private function calculateTieredPercentageDiscount(array $tier, int $quantity, float $unitPrice): float
    {
        $tieredRates = $tier['tiered_rates'] ?? [];
        if (empty($tieredRates)) {
            return 0;
        }

        // Sort tiered rates by quantity threshold
        usort($tieredRates, function ($a, $b) {
            return ($a['from_quantity'] ?? 0) <=> ($b['from_quantity'] ?? 0);
        });

        $totalDiscount = 0;
        $remainingQuantity = $quantity;

        foreach ($tieredRates as $rate) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $fromQuantity = $rate['from_quantity'] ?? 0;
            $toQuantity = $rate['to_quantity'] ?? PHP_INT_MAX;
            $discountPercentage = $rate['discount_percentage'] ?? 0;

            // Skip if we haven't reached this tier yet
            if ($quantity < $fromQuantity) {
                continue;
            }

            // Calculate quantity in this tier
            $tierStartQuantity = max(0, $fromQuantity - ($quantity - $remainingQuantity));
            $tierEndQuantity = min($remainingQuantity, $toQuantity - ($quantity - $remainingQuantity));
            $tierQuantity = max(0, $tierEndQuantity - $tierStartQuantity);

            if ($tierQuantity > 0) {
                $tierDiscount = ($unitPrice * $tierQuantity * $discountPercentage) / 100;
                $totalDiscount += $tierDiscount;
                $remainingQuantity -= $tierQuantity;
            }
        }

        return $totalDiscount;
    }
}