<?php

declare(strict_types=1);

namespace App\Services\Campaign\Handlers;

use App\Contracts\Campaign\CampaignHandlerInterface;
use App\Enums\Campaign\CampaignType;
use App\Models\Campaign;
use App\Models\ProductVariant;
use App\Services\ProductCacheService;
use App\ValueObjects\Campaign\CampaignResult;
use App\ValueObjects\Campaign\CartContext;
use App\ValueObjects\Pricing\Discount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CrossSellHandler implements CampaignHandlerInterface
{
    public function __construct(
        private ProductCacheService $productCacheService
    ) {}

    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::CROSS_SELL->value;
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

            // Cross-sell validation and calculation
            $crossSellResult = $this->calculateCrossSellBenefit($campaign, $context);
            
            if (!$crossSellResult['applicable']) {
                return CampaignResult::failed($crossSellResult['reason']);
            }

            Log::info('Cross-sell campaign applied', [
                'campaign_id' => $campaign->id,
                'customer_id' => $context->getCustomerId(),
                'trigger_products' => $crossSellResult['trigger_products'],
                'suggested_products' => $crossSellResult['suggested_products'],
                'benefit_type' => $crossSellResult['benefit_type']
            ]);

            // Return result based on benefit type
            return match ($crossSellResult['benefit_type']) {
                'discount' => CampaignResult::discount(
                    new Discount($crossSellResult['discount_amount'], 'Cross-sell Discount: ' . $campaign->name),
                    "Cross-sell kampanyası: {$campaign->name} - İndirim uygulandı"
                ),
                'free_product' => CampaignResult::freeItems(
                    $crossSellResult['free_items'],
                    "Cross-sell kampanyası: {$campaign->name} - Hediye ürün eklendi"
                ),
                'combined' => CampaignResult::combined(
                    new Discount($crossSellResult['discount_amount'], 'Cross-sell Combined: ' . $campaign->name),
                    $crossSellResult['free_items'],
                    "Cross-sell kampanyası: {$campaign->name} - İndirim ve hediye ürün"
                ),
                default => CampaignResult::failed('Unknown benefit type')
            };

        } catch (\Exception $e) {
            Log::error('Cross-sell handler failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return CampaignResult::failed('Cross-sell calculation failed');
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

        // Must have valid cross-sell rules
        $rules = $campaign->rules ?? [];
        if (empty($rules['trigger_products']) || !is_array($rules['trigger_products'])) {
            return false;
        }

        if (empty($rules['suggested_products']) || !is_array($rules['suggested_products'])) {
            return false;
        }

        return true;
    }

    private function validateContext(CartContext $context): bool
    {
        return $context->getItems()->isNotEmpty() && $context->getTotalAmount() > 0;
    }

    private function calculateCrossSellBenefit(Campaign $campaign, CartContext $context): array
    {
        $rules = $campaign->rules ?? [];
        $rewards = $campaign->rewards ?? [];

        $triggerProducts = $rules['trigger_products'] ?? [];
        $suggestedProducts = $rules['suggested_products'] ?? [];
        $requireAllTriggers = $rules['require_all_triggers'] ?? false;
        $minTriggerQuantity = $rules['min_trigger_quantity'] ?? 1;

        // Check if trigger products are in cart
        $triggerResult = $this->checkTriggerProducts($context, $triggerProducts, $requireAllTriggers, $minTriggerQuantity);
        
        if (!$triggerResult['triggered']) {
            return [
                'applicable' => false,
                'reason' => $triggerResult['reason']
            ];
        }

        // Check if suggested products are in cart
        $suggestedResult = $this->checkSuggestedProducts($context, $suggestedProducts);
        
        if (!$suggestedResult['found_any']) {
            return [
                'applicable' => false,
                'reason' => 'Cross-sell için önerilen ürünler sepette bulunamadı'
            ];
        }

        // Calculate benefits
        $benefitType = $rewards['benefit_type'] ?? 'discount'; // 'discount', 'free_product', 'combined'
        $benefits = $this->calculateBenefits($campaign, $context, $suggestedResult['found_products']);

        return [
            'applicable' => true,
            'trigger_products' => $triggerResult['found_triggers'],
            'suggested_products' => $suggestedResult['found_products'],
            'benefit_type' => $benefitType,
            'discount_amount' => $benefits['discount_amount'] ?? 0,
            'free_items' => $benefits['free_items'] ?? [],
        ];
    }

    private function checkTriggerProducts(CartContext $context, array $triggerProducts, bool $requireAll, int $minQuantity): array
    {
        $cartItems = $context->getItems();
        $foundTriggers = [];
        $totalTriggerQuantity = 0;

        foreach ($triggerProducts as $triggerProduct) {
            $productId = $triggerProduct['product_id'];
            $requiredQuantity = $triggerProduct['min_quantity'] ?? $minQuantity;

            $cartItem = $cartItems->firstWhere('product_id', $productId);
            
            if ($cartItem && $cartItem['quantity'] >= $requiredQuantity) {
                $foundTriggers[] = [
                    'product_id' => $productId,
                    'quantity' => $cartItem['quantity'],
                    'required_quantity' => $requiredQuantity
                ];
                $totalTriggerQuantity += $cartItem['quantity'];
            } elseif ($requireAll) {
                return [
                    'triggered' => false,
                    'reason' => "Cross-sell tetikleyici ürün bulunamadı: {$productId}"
                ];
            }
        }

        if (empty($foundTriggers)) {
            return [
                'triggered' => false,
                'reason' => 'Cross-sell tetikleyici ürünlerden hiçbiri sepette bulunamadı'
            ];
        }

        return [
            'triggered' => true,
            'found_triggers' => $foundTriggers,
            'total_trigger_quantity' => $totalTriggerQuantity
        ];
    }

    private function checkSuggestedProducts(CartContext $context, array $suggestedProducts): array
    {
        $cartItems = $context->getItems();
        $foundProducts = [];

        foreach ($suggestedProducts as $suggestedProduct) {
            $productId = $suggestedProduct['product_id'];
            $cartItem = $cartItems->firstWhere('product_id', $productId);

            if ($cartItem) {
                $foundProducts[] = [
                    'product_id' => $productId,
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price'],
                    'benefit_config' => $suggestedProduct
                ];
            }
        }

        return [
            'found_any' => !empty($foundProducts),
            'found_products' => $foundProducts
        ];
    }

    private function calculateBenefits(Campaign $campaign, CartContext $context, array $foundProducts): array
    {
        $rewards = $campaign->rewards ?? [];
        $benefitType = $rewards['benefit_type'] ?? 'discount';
        
        $discountAmount = 0;
        $freeItems = [];

        foreach ($foundProducts as $product) {
            $benefitConfig = $product['benefit_config'];
            
            switch ($benefitType) {
                case 'discount':
                    $discountAmount += $this->calculateProductDiscount($product, $benefitConfig);
                    break;
                    
                case 'free_product':
                    $freeItems = array_merge($freeItems, $this->calculateFreeProducts($product, $benefitConfig));
                    break;
                    
                case 'combined':
                    $discountAmount += $this->calculateProductDiscount($product, $benefitConfig);
                    $freeItems = array_merge($freeItems, $this->calculateFreeProducts($product, $benefitConfig));
                    break;
            }
        }

        // Apply maximum limits
        $maxDiscount = $rewards['max_discount'] ?? null;
        if ($maxDiscount && $discountAmount > $maxDiscount) {
            $discountAmount = $maxDiscount;
        }

        // Limit discount to cart total
        $cartTotal = $context->getTotalAmount();
        $discountAmount = min($discountAmount, $cartTotal);

        return [
            'discount_amount' => $discountAmount,
            'free_items' => $freeItems
        ];
    }

    private function calculateProductDiscount(array $product, array $benefitConfig): float
    {
        $discountType = $benefitConfig['discount_type'] ?? 'percentage';
        $discountValue = $benefitConfig['discount_value'] ?? 0;
        $productPrice = $product['price'];
        $quantity = $product['quantity'];
        
        $maxQuantity = $benefitConfig['max_quantity'] ?? $quantity;
        $applicableQuantity = min($quantity, $maxQuantity);

        return match ($discountType) {
            'percentage' => ($productPrice * $applicableQuantity * $discountValue) / 100,
            'fixed' => $discountValue * $applicableQuantity,
            'fixed_per_item' => $discountValue * $applicableQuantity,
            default => 0
        };
    }

    private function calculateFreeProducts(array $product, array $benefitConfig): array
    {
        $freeItems = [];
        $freeProductId = $benefitConfig['free_product_id'] ?? null;
        $freeQuantity = $benefitConfig['free_quantity'] ?? 1;
        $maxFreeQuantity = $benefitConfig['max_free_quantity'] ?? $freeQuantity;

        if (!$freeProductId) {
            return $freeItems;
        }

        // Calculate how many free items based on purchased quantity
        $purchasedQuantity = $product['quantity'];
        $freePerPurchased = $benefitConfig['free_per_purchased'] ?? 1;
        
        $totalFreeQuantity = intval($purchasedQuantity / $freePerPurchased) * $freeQuantity;
        $totalFreeQuantity = min($totalFreeQuantity, $maxFreeQuantity);

        if ($totalFreeQuantity > 0) {
            // Get product details from cache
            $freeProduct = $this->productCacheService->getProductVariant($freeProductId);
            
            if ($freeProduct) {
                $freeItems[] = [
                    'product_id' => $freeProductId,
                    'product_name' => $freeProduct->product->name ?? 'Unknown Product',
                    'variant_name' => $freeProduct->name ?? 'Default Variant',
                    'quantity' => $totalFreeQuantity,
                    'unit_price' => $freeProduct->price ?? 0,
                    'total_value' => ($freeProduct->price ?? 0) * $totalFreeQuantity
                ];
            }
        }

        return $freeItems;
    }
}