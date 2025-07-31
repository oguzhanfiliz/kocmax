<?php

declare(strict_types=1);

namespace App\Contracts\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

interface CartStrategyInterface
{
    /**
     * Add item to cart using strategy-specific logic
     */
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void;

    /**
     * Update item quantity using strategy-specific logic
     */
    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void;

    /**
     * Remove item from cart using strategy-specific logic
     */
    public function removeItem(Cart $cart, CartItem $item): void;

    /**
     * Clear all items from cart using strategy-specific logic
     */
    public function clear(Cart $cart): void;

    /**
     * Strategy-specific validation for cart operations
     */
    public function validateOperation(Cart $cart, string $operation, array $context = []): bool;
}