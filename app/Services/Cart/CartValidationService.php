<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\ValueObjects\Cart\CartValidationResult;
use App\Services\Pricing\CustomerTypeDetector;

class CartValidationService
{
    public function __construct(
        private CustomerTypeDetector $customerTypeDetector
    ) {}

    public function validateAddItem(Cart $cart, ProductVariant $variant, int $quantity): CartValidationResult
    {
        $errors = [];

        // Basic quantity validation
        if ($quantity <= 0) {
            $errors[] = "Quantity must be greater than 0";
        }

        if ($quantity > 999) {
            $errors[] = "Maximum quantity per item is 999";
        }

        // Stock validation
        if ($variant->stock < $quantity) {
            $errors[] = "Insufficient stock. Available: {$variant->stock}, Requested: {$quantity}";
        }

        // Product availability validation
        if (!$variant->product->is_active) {
            $errors[] = "Product is not available";
        }

        // Check if item already exists in cart
        $existingItem = $cart->items()
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($existingItem) {
            $totalQuantity = $existingItem->quantity + $quantity;
            if ($totalQuantity > $variant->stock) {
                $errors[] = "Total quantity ({$totalQuantity}) exceeds available stock ({$variant->stock})";
            }
        }

        // B2B specific validations
        if ($cart->user && $cart->user->isDealer()) {
            $b2bValidation = $this->validateB2BConstraints($cart, $variant, $quantity);
            $errors = array_merge($errors, $b2bValidation);
        }

        return new CartValidationResult(
            isValid: empty($errors),
            errors: $errors
        );
    }

    public function validateQuantityUpdate(CartItem $item, int $newQuantity): CartValidationResult
    {
        $errors = [];

        if ($newQuantity < 0) {
            $errors[] = "Quantity cannot be negative";
        }

        if ($newQuantity > 999) {
            $errors[] = "Maximum quantity per item is 999";
        }

        if ($newQuantity > 0 && $newQuantity > $item->productVariant->stock) {
            $errors[] = "Insufficient stock. Available: {$item->productVariant->stock}, Requested: {$newQuantity}";
        }

        if (!$item->productVariant->product->is_active) {
            $errors[] = "Product is no longer available";
        }

        return new CartValidationResult(
            isValid: empty($errors),
            errors: $errors
        );
    }

    public function validateForCheckout(Cart $cart): CartValidationResult
    {
        $errors = [];

        // Empty cart check
        if ($cart->items->isEmpty()) {
            $errors[] = "Cart is empty";
        }

        // Validate each item
        foreach ($cart->items as $item) {
            $itemValidation = $this->validateCartItem($item);
            if (!$itemValidation->isValid()) {
                $errors = array_merge($errors, $itemValidation->getErrors());
            }
        }

        // B2B specific checkout validations
        if ($cart->user && $cart->user->isDealer()) {
            $b2bCheckoutValidation = $this->validateB2BCheckout($cart);
            $errors = array_merge($errors, $b2bCheckoutValidation);
        }

        // Minimum order amount validation
        if ($cart->user && $cart->user->pricingTier) {
            $minOrderAmount = $cart->user->pricingTier->min_order_amount ?? 0;
            if ($minOrderAmount > 0 && $cart->total_amount < $minOrderAmount) {
                $errors[] = "Minimum order amount is {$minOrderAmount} TL";
            }
        }

        return new CartValidationResult(
            isValid: empty($errors),
            errors: $errors
        );
    }

    private function validateCartItem(CartItem $item): CartValidationResult
    {
        $errors = [];

        // Product availability
        if (!$item->product->is_active) {
            $errors[] = "Product '{$item->product->name}' is no longer available";
        }

        // Variant availability
        if (!$item->productVariant->product->is_active) {
            $errors[] = "Product variant is no longer available";
        }

        // Stock availability
        if ($item->quantity > $item->productVariant->stock) {
            $errors[] = "Insufficient stock for '{$item->product->name}'. Available: {$item->productVariant->stock}";
        }

        // Price validation (detect stale pricing)
        if ($item->price_calculated_at && $item->price_calculated_at->lt(now()->subHours(24))) {
            $errors[] = "Price information is outdated for '{$item->product->name}'";
        }

        return new CartValidationResult(
            isValid: empty($errors),
            errors: $errors
        );
    }

    private function validateB2BConstraints(Cart $cart, ProductVariant $variant, int $quantity): array
    {
        $errors = [];

        // Credit limit validation would go here
        // This requires integration with a credit management system
        
        // Minimum quantity for B2B (if applicable)
        if ($variant->product->min_b2b_quantity && $quantity < $variant->product->min_b2b_quantity) {
            $errors[] = "Minimum B2B quantity for this product is {$variant->product->min_b2b_quantity}";
        }

        return $errors;
    }

    private function validateB2BCheckout(Cart $cart): array
    {
        $errors = [];

        // Dealer approval check
        if (!$cart->user->is_approved_dealer) {
            $errors[] = "Dealer account is not approved for checkout";
        }

        // Credit limit validation
        $totalAmount = $cart->total_amount;
        $creditLimit = $cart->user->credit_limit ?? 0;
        $currentDebt = $cart->user->current_debt ?? 0;

        if ($creditLimit > 0 && ($currentDebt + $totalAmount) > $creditLimit) {
            $availableCredit = $creditLimit - $currentDebt;
            $errors[] = "Insufficient credit limit. Available: {$availableCredit} TL, Required: {$totalAmount} TL";
        }

        return $errors;
    }
}