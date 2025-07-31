<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Services\PricingService;
use App\Services\Pricing\CustomerTypeDetector;
use App\ValueObjects\Cart\CartSummary;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CartPriceCoordinator
{
    public function __construct(
        private PricingService $pricingService,
        private CustomerTypeDetector $customerTypeDetector
    ) {}

    public function updateCartPricing(Cart $cart): CartSummary
    {
        try {
            // Update customer type detection
            $customerType = $this->customerTypeDetector->detect($cart->user);
            
            // Update individual item pricing
            $this->updateAllItemPrices($cart);
            
            // Calculate cart summary
            $summary = $this->calculateCartSummary($cart);
            
            // Update cart with calculated values
            $cart->update([
                'customer_type' => $customerType->value,
                'subtotal_amount' => $summary->getSubtotal(),
                'total_amount' => $summary->getTotal(),
                'discounted_amount' => $summary->getTotal(),
                'pricing_calculated_at' => now(),
                'last_pricing_update' => now(),
                'applied_discounts' => $summary->getAppliedDiscounts(),
            ]);

            Log::info('Cart pricing updated successfully', [
                'cart_id' => $cart->id,
                'customer_type' => $customerType->value,
                'total_amount' => $summary->getTotal()
            ]);

            return $summary;

        } catch (\Exception $e) {
            Log::error('Failed to update cart pricing', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function calculateCartSummary(Cart $cart): CartSummary
    {
        $cacheKey = "cart_summary_{$cart->id}_{$cart->updated_at->timestamp}";
        
        return Cache::remember($cacheKey, 300, function () use ($cart) {
            return $this->performCartSummaryCalculation($cart);
        });
    }

    public function refreshAllPrices(Cart $cart): void
    {
        $this->updateAllItemPrices($cart);
        $this->updateCartPricing($cart);
        
        // Clear cache for this cart
        $cacheKey = "cart_summary_{$cart->id}_*";
        Cache::flush(); // In production, use more specific cache invalidation
    }

    public function calculateItemPrice(CartItem $item, ?User $user = null): PriceResult
    {
        return $this->pricingService->calculatePrice(
            $item->productVariant,
            $item->quantity,
            $user ?? $item->cart->user
        );
    }

    private function updateAllItemPrices(Cart $cart): void
    {
        $cart->load(['items.productVariant', 'user']);

        foreach ($cart->items as $item) {
            $this->updateItemPrice($item, $cart->user);
        }
    }

    private function updateItemPrice(CartItem $item, ?User $user): void
    {
        try {
            $priceResult = $this->pricingService->calculatePrice(
                $item->productVariant,
                $item->quantity,
                $user
            );

            $appliedDiscounts = [];
            if ($priceResult->getDiscount()) {
                $appliedDiscounts[] = [
                    'type' => 'pricing_service',
                    'amount' => $priceResult->getDiscount()->getAmount(),
                    'description' => $priceResult->getDiscount()->getDescription() ?? 'Automatic discount'
                ];
            }

            $item->update([
                'base_price' => $priceResult->getBasePrice()->getAmount(),
                'calculated_price' => $priceResult->getFinalPrice()->getAmount(),
                'price' => $priceResult->getFinalPrice()->getAmount(), // For backward compatibility
                'discounted_price' => $priceResult->getFinalPrice()->getAmount(),
                'unit_discount' => $priceResult->getDiscount()?->getAmount() ?? 0,
                'total_discount' => ($priceResult->getDiscount()?->getAmount() ?? 0) * $item->quantity,
                'applied_discounts' => $appliedDiscounts,
                'price_calculated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update item price', [
                'item_id' => $item->id,
                'cart_id' => $item->cart_id,
                'error' => $e->getMessage()
            ]);
            
            // Keep existing price if calculation fails
            if (!$item->price_calculated_at) {
                $item->update([
                    'calculated_price' => $item->productVariant->price,
                    'price' => $item->productVariant->price,
                    'price_calculated_at' => now(),
                ]);
            }
        }
    }

    private function performCartSummaryCalculation(Cart $cart): CartSummary
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $itemDetails = [];
        
        // Calculate totals from items
        foreach ($cart->items as $item) {
            $itemSubtotal = ($item->calculated_price ?? $item->price ?? 0) * $item->quantity;
            $itemDiscount = $item->total_discount ?? 0;
            
            $subtotal += $itemSubtotal;
            $totalDiscount += $itemDiscount;
            
            $itemDetails[] = [
                'item_id' => $item->id,
                'product_name' => $item->product->name,
                'variant_name' => $item->productVariant->name ?? '',
                'quantity' => $item->quantity,
                'base_price' => $item->base_price ?? $item->productVariant->price,
                'calculated_price' => $item->calculated_price ?? $item->price,
                'unit_discount' => $item->unit_discount ?? 0,
                'total_discount' => $item->total_discount ?? 0,
                'subtotal' => $itemSubtotal,
                'applied_discounts' => $item->applied_discounts ?? []
            ];
        }

        // Apply cart-level discounts (coupons, etc.)
        $cartLevelDiscount = $cart->coupon_discount ?? 0;
        $totalDiscount += $cartLevelDiscount;

        // Collect all applied discounts
        $appliedDiscounts = [];
        
        // Add item-level discounts
        foreach ($cart->items as $item) {
            if ($item->applied_discounts) {
                $appliedDiscounts = array_merge($appliedDiscounts, $item->applied_discounts);
            }
        }
        
        // Add cart-level discounts
        if ($cartLevelDiscount > 0) {
            $appliedDiscounts[] = [
                'type' => 'coupon',
                'code' => $cart->coupon_code,
                'amount' => $cartLevelDiscount,
                'description' => "Coupon: {$cart->coupon_code}"
            ];
        }

        $finalTotal = max(0, $subtotal - $totalDiscount);

        return new CartSummary(
            subtotal: $subtotal,
            discount: $totalDiscount,
            total: $finalTotal,
            itemCount: $cart->items->sum('quantity'),
            itemDetails: $itemDetails,
            appliedDiscounts: $appliedDiscounts
        );
    }
}