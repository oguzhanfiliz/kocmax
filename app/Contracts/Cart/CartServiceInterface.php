<?php

declare(strict_types=1);

namespace App\Contracts\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Cart\CartSummary;
use App\ValueObjects\Cart\CheckoutContext;

interface CartServiceInterface
{
    /**
     * Add item to cart with quantity validation
     */
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity = 1): void;

    /**
     * Update item quantity in cart
     */
    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void;

    /**
     * Remove item from cart
     */
    public function removeItem(Cart $cart, CartItem $item): void;

    /**
     * Clear all items from cart
     */
    public function clearCart(Cart $cart): void;

    /**
     * Calculate cart summary with pricing
     */
    public function calculateSummary(Cart $cart): CartSummary;

    /**
     * Refresh all pricing in cart
     */
    public function refreshPricing(Cart $cart): void;

    /**
     * Prepare cart data for checkout
     */
    public function prepareCheckout(Cart $cart): CheckoutContext;

    /**
     * Migrate guest cart to authenticated user
     */
    public function migrateGuestCart(string $sessionId, User $user): ?Cart;
}