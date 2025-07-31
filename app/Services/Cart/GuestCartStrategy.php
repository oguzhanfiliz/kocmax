<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartStrategyInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class GuestCartStrategy implements CartStrategyInterface
{
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        // First, handle session storage
        $this->addItemToSession($cart, $variant, $quantity);
        
        // Then sync with database
        $this->syncCartFromSession($cart);
    }

    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($cart, $item);
            return;
        }

        // Update session
        $this->updateQuantityInSession($cart, $item, $quantity);
        
        // Sync with database
        $this->syncCartFromSession($cart);
    }

    public function removeItem(Cart $cart, CartItem $item): void
    {
        // Remove from session
        $this->removeItemFromSession($cart, $item);
        
        // Sync with database
        $this->syncCartFromSession($cart);
    }

    public function clear(Cart $cart): void
    {
        $sessionKey = $this->getSessionKey($cart);
        Session::forget($sessionKey);
        
        // Clear database
        $cart->items()->delete();
        $cart->update([
            'total_amount' => 0,
            'discounted_amount' => 0,
            'subtotal_amount' => 0,
            'coupon_code' => null,
            'coupon_discount' => 0,
            'applied_discounts' => null,
            'pricing_calculated_at' => null,
            'last_pricing_update' => null,
            'pricing_context' => null,
        ]);
        
        Log::debug('Cleared guest cart', [
            'cart_id' => $cart->id,
            'session_id' => $cart->session_id
        ]);
    }

    public function validateOperation(Cart $cart, string $operation, array $context = []): bool
    {
        // Guest carts must have session_id and no user_id
        if (!$cart->session_id || $cart->user_id) {
            return false;
        }

        switch ($operation) {
            case 'add_item':
                return $this->validateAddItem($cart, $context);
            case 'update_quantity':
                return $this->validateUpdateQuantity($cart, $context);
            case 'remove_item':
                return $this->validateRemoveItem($cart, $context);
            case 'clear':
                return true; // Guest carts can always be cleared
            default:
                return false;
        }
    }

    private function addItemToSession(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        $sessionKey = $this->getSessionKey($cart);
        $cartData = Session::get($sessionKey, ['items' => []]);

        $existingItemKey = $this->findExistingItemInSession($cartData['items'], $variant->id);
        
        if ($existingItemKey !== null) {
            $cartData['items'][$existingItemKey]['quantity'] += $quantity;
            $cartData['items'][$existingItemKey]['updated_at'] = now()->toISOString();
        } else {
            $cartData['items'][] = [
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'price' => $variant->price,
                'added_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];
        }

        $cartData['updated_at'] = now()->toISOString();
        Session::put($sessionKey, $cartData);
        
        Log::debug('Added item to guest cart session', [
            'cart_id' => $cart->id,
            'session_id' => $cart->session_id,
            'variant_id' => $variant->id,
            'quantity' => $quantity
        ]);
    }

    private function updateQuantityInSession(Cart $cart, CartItem $item, int $quantity): void
    {
        $sessionKey = $this->getSessionKey($cart);
        $cartData = Session::get($sessionKey, ['items' => []]);

        $itemKey = $this->findExistingItemInSession($cartData['items'], $item->product_variant_id);
        
        if ($itemKey !== null) {
            $cartData['items'][$itemKey]['quantity'] = $quantity;
            $cartData['items'][$itemKey]['updated_at'] = now()->toISOString();
            $cartData['updated_at'] = now()->toISOString();
            
            Session::put($sessionKey, $cartData);
        }
    }

    private function removeItemFromSession(Cart $cart, CartItem $item): void
    {
        $sessionKey = $this->getSessionKey($cart);
        $cartData = Session::get($sessionKey, ['items' => []]);

        $cartData['items'] = array_values(array_filter($cartData['items'], function ($sessionItem) use ($item) {
            return $sessionItem['product_variant_id'] !== $item->product_variant_id;
        }));

        $cartData['updated_at'] = now()->toISOString();
        Session::put($sessionKey, $cartData);
    }

    private function syncCartFromSession(Cart $cart): void
    {
        $sessionKey = $this->getSessionKey($cart);
        $cartData = Session::get($sessionKey, ['items' => []]);

        // Clear existing cart items
        $cart->items()->delete();

        // Recreate from session
        foreach ($cartData['items'] as $sessionItem) {
            $cart->items()->create([
                'product_id' => $sessionItem['product_id'],
                'product_variant_id' => $sessionItem['product_variant_id'],
                'quantity' => $sessionItem['quantity'],
                'price' => $sessionItem['price']
            ]);
        }

        $cart->touch();
        
        Log::debug('Synced guest cart from session to database', [
            'cart_id' => $cart->id,
            'session_id' => $cart->session_id,
            'item_count' => count($cartData['items'])
        ]);
    }

    private function findExistingItemInSession(array $items, int $variantId): ?int
    {
        foreach ($items as $index => $item) {
            if ($item['product_variant_id'] === $variantId) {
                return $index;
            }
        }
        
        return null;
    }

    private function getSessionKey(Cart $cart): string
    {
        return "guest_cart_{$cart->session_id}";
    }

    private function validateAddItem(Cart $cart, array $context): bool
    {
        return isset($context['variant']) && isset($context['quantity']) && $context['quantity'] > 0;
    }

    private function validateUpdateQuantity(Cart $cart, array $context): bool
    {
        return isset($context['item']) && isset($context['quantity']) && $context['quantity'] >= 0;
    }

    private function validateRemoveItem(Cart $cart, array $context): bool
    {
        return isset($context['item']) && $context['item']->cart_id === $cart->id;
    }
}