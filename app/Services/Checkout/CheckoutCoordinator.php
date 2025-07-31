<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Cart;
use App\Models\Order;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use App\Services\Order\OrderValidationService;
use App\ValueObjects\Cart\CheckoutContext;
use App\Exceptions\Checkout\CheckoutValidationException;
use App\Exceptions\Checkout\CheckoutProcessingException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutCoordinator
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private CheckoutValidationService $validationService
    ) {}

    public function processCheckout(Cart $cart, array $checkoutData): Order
    {
        try {
            return DB::transaction(function() use ($cart, $checkoutData) {
                // Step 1: Prepare checkout context from cart
                $checkoutContext = $this->cartService->prepareCheckout($cart);
                
                // Step 2: Validate checkout data
                $validation = $this->validationService->validateCheckout($checkoutContext, $checkoutData);
                if (!$validation->isValid()) {
                    throw new CheckoutValidationException($validation->getErrors());
                }
                
                Log::info('Starting checkout process', [
                    'cart_id' => $cart->id,
                    'total_amount' => $checkoutContext->getTotalAmount(),
                    'customer_type' => $checkoutContext->getCustomerType()
                ]);
                
                // Step 3: Create order from checkout context
                $order = $this->orderService->createFromCheckout($checkoutContext, $checkoutData);
                
                // Step 4: Clear cart after successful order creation
                $this->clearCartAfterCheckout($cart);
                
                Log::info('Checkout completed successfully', [
                    'cart_id' => $cart->id,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
                
                return $order;
            });

        } catch (\Exception $e) {
            Log::error('Checkout process failed', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage(),
                'checkout_data' => $this->sanitizeCheckoutData($checkoutData)
            ]);
            
            throw new CheckoutProcessingException(
                'Failed to process checkout: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    public function validateCheckout(Cart $cart, array $checkoutData): \App\ValueObjects\Order\OrderValidationResult
    {
        try {
            // Validate cart state
            $cartValidation = $this->cartService->validateOrder($cart);
            if (!$cartValidation->isValid()) {
                return \App\ValueObjects\Order\OrderValidationResult::invalid($cartValidation->getErrors());
            }

            // Prepare checkout context
            $checkoutContext = $this->cartService->prepareCheckout($cart);
            
            // Validate checkout data
            return $this->validationService->validateCheckout($checkoutContext, $checkoutData);

        } catch (\Exception $e) {
            Log::error('Checkout validation failed', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage()
            ]);
            
            return \App\ValueObjects\Order\OrderValidationResult::invalid([
                'Failed to validate checkout: ' . $e->getMessage()
            ]);
        }
    }

    public function processGuestCheckout(array $cartData, array $checkoutData): Order
    {
        try {
            return DB::transaction(function() use ($cartData, $checkoutData) {
                // Create temporary cart from cart data
                $cart = $this->createTempCartFromData($cartData);
                
                // Process checkout
                $order = $this->processCheckout($cart, $checkoutData);
                
                // Clean up temporary cart
                $cart->delete();
                
                return $order;
            });

        } catch (\Exception $e) {
            Log::error('Guest checkout process failed', [
                'error' => $e->getMessage(),
                'checkout_data' => $this->sanitizeCheckoutData($checkoutData)
            ]);
            
            throw new CheckoutProcessingException(
                'Failed to process guest checkout: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    public function estimateCheckout(Cart $cart, array $checkoutData): array
    {
        try {
            // Prepare checkout context
            $checkoutContext = $this->cartService->prepareCheckout($cart);
            
            // Calculate shipping costs
            $shippingCost = $this->calculateShippingCost($checkoutContext, $checkoutData);
            
            // Calculate tax
            $taxAmount = $this->calculateTax($checkoutContext, $checkoutData);
            
            // Calculate final total
            $subtotal = $checkoutContext->getSubtotal();
            $discount = $checkoutContext->getDiscount();
            $finalTotal = $subtotal - $discount + $shippingCost + $taxAmount;
            
            return [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'final_total' => $finalTotal,
                'currency' => 'TRY',
                'estimated' => true
            ];

        } catch (\Exception $e) {
            Log::error('Checkout estimation failed', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    public function rollbackFailedCheckout(Cart $cart, ?Order $order = null): void
    {
        try {
            Log::info('Rolling back failed checkout', [
                'cart_id' => $cart->id,
                'order_id' => $order?->id
            ]);

            DB::transaction(function() use ($cart, $order) {
                // If order was created, cancel it
                if ($order) {
                    try {
                        $this->orderService->cancelOrder($order, null, 'Checkout rollback');
                    } catch (\Exception $e) {
                        Log::error('Failed to cancel order during rollback', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                // Restore cart state if needed
                // Cart should still be available for retry
                Log::info('Rollback completed', [
                    'cart_id' => $cart->id,
                    'order_cancelled' => $order ? 'yes' : 'no'
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Rollback failed', [
                'cart_id' => $cart->id,
                'order_id' => $order?->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function clearCartAfterCheckout(Cart $cart): void
    {
        try {
            // Clear cart items and reset totals
            $this->cartService->clearCart($cart);
            
            Log::debug('Cart cleared after successful checkout', [
                'cart_id' => $cart->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear cart after checkout', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage()
            ]);
            
            // Don't throw exception here as checkout was successful
            // This is cleanup that can fail without affecting the order
        }
    }

    private function createTempCartFromData(array $cartData): Cart
    {
        // Create a temporary cart for guest checkout
        $cart = Cart::create([
            'session_id' => 'temp_' . uniqid(),
            'user_id' => null,
            'total_amount' => $cartData['total_amount'] ?? 0,
            'discounted_amount' => $cartData['discounted_amount'] ?? 0,
            'subtotal_amount' => $cartData['subtotal_amount'] ?? 0,
        ]);

        // Create cart items
        foreach ($cartData['items'] ?? [] as $itemData) {
            $cart->items()->create([
                'product_id' => $itemData['product_id'],
                'product_variant_id' => $itemData['product_variant_id'] ?? null,
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'discounted_price' => $itemData['discounted_price'] ?? $itemData['price'],
            ]);
        }

        return $cart->fresh(['items']);
    }

    private function calculateShippingCost(CheckoutContext $context, array $checkoutData): float
    {
        // Mock shipping calculation
        // In real implementation, this would integrate with shipping providers
        
        $baseShipping = 15.0; // Base shipping cost
        $country = $checkoutData['shipping_country'] ?? 'TR';
        
        // International shipping
        if ($country !== 'TR') {
            $baseShipping *= 3;
        }
        
        // Free shipping for high-value orders
        if ($context->getTotalAmount() > 500) {
            return 0.0;
        }
        
        return $baseShipping;
    }

    private function calculateTax(CheckoutContext $context, array $checkoutData): float
    {
        // Mock tax calculation
        // In real implementation, this would use proper tax calculation rules
        
        $taxRate = 0.18; // 18% VAT for Turkey
        $country = $checkoutData['billing_country'] ?? 'TR';
        
        // Different tax rates for different countries
        if ($country !== 'TR') {
            $taxRate = 0.20; // 20% for EU
        }
        
        // B2B customers might have different tax treatment
        if ($context->isB2B()) {
            // Business tax handling
            $taxNumber = $checkoutData['billing_tax_number'] ?? null;
            if ($taxNumber) {
                // Valid tax number might affect tax calculation
                return ($context->getSubtotal() - $context->getDiscount()) * $taxRate;
            }
        }
        
        return ($context->getSubtotal() - $context->getDiscount()) * $taxRate;
    }

    private function sanitizeCheckoutData(array $checkoutData): array
    {
        // Remove sensitive payment information from logs
        $sanitized = $checkoutData;
        
        $sensitiveFields = [
            'card_number',
            'card_cvv',
            'card_expiry',
            'payment_token',
            'banking_credentials'
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '[REDACTED]';
            }
        }
        
        return $sanitized;
    }
}