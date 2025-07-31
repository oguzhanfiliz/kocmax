<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\ValueObjects\Cart\CheckoutContext;
use Illuminate\Support\Facades\Log;

class OrderCreationService
{
    public function createOrder(CheckoutContext $context, array $orderData): Order
    {
        $orderNumber = $this->generateOrderNumber();
        
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $context->getCustomerType() !== 'guest' ? $orderData['user_id'] ?? null : null,
            'customer_type' => strtoupper($context->getCustomerType()),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $orderData['payment_method'] ?? 'card',
            
            // Amounts from cart summary
            'subtotal' => $context->getSummary()->getSubtotal(),
            'tax_amount' => $orderData['tax_amount'] ?? 0,
            'shipping_amount' => $orderData['shipping_amount'] ?? 0,
            'discount_amount' => $context->getSummary()->getDiscount(),
            'total_amount' => $context->getTotalAmount(),
            'currency_code' => $orderData['currency_code'] ?? 'TRY',
            'coupon_code' => $orderData['coupon_code'] ?? null,
            'notes' => $orderData['notes'] ?? null,
            
            // Shipping Address
            'shipping_name' => $orderData['shipping_name'] ?? null,
            'shipping_email' => $orderData['shipping_email'] ?? null,
            'shipping_phone' => $orderData['shipping_phone'] ?? null,
            'shipping_address' => $orderData['shipping_address'] ?? null,
            'shipping_city' => $orderData['shipping_city'] ?? null,
            'shipping_state' => $orderData['shipping_state'] ?? null,
            'shipping_zip' => $orderData['shipping_zip'] ?? null,
            'shipping_country' => $orderData['shipping_country'] ?? 'TR',
            
            // Billing Address
            'billing_name' => $orderData['billing_name'] ?? $orderData['shipping_name'] ?? null,
            'billing_email' => $orderData['billing_email'] ?? $orderData['shipping_email'] ?? null,
            'billing_phone' => $orderData['billing_phone'] ?? $orderData['shipping_phone'] ?? null,
            'billing_address' => $orderData['billing_address'] ?? $orderData['shipping_address'] ?? null,
            'billing_city' => $orderData['billing_city'] ?? $orderData['shipping_city'] ?? null,
            'billing_state' => $orderData['billing_state'] ?? $orderData['shipping_state'] ?? null,
            'billing_zip' => $orderData['billing_zip'] ?? $orderData['shipping_zip'] ?? null,
            'billing_country' => $orderData['billing_country'] ?? $orderData['shipping_country'] ?? 'TR',
            'billing_tax_number' => $orderData['billing_tax_number'] ?? null,
            'billing_tax_office' => $orderData['billing_tax_office'] ?? null,
        ]);

        Log::info('Order entity created', [
            'order_id' => $order->id,
            'order_number' => $orderNumber,
            'customer_type' => $context->getCustomerType()
        ]);

        return $order;
    }

    public function createOrderItems(Order $order, array $cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem['product_id'],
                'product_variant_id' => $cartItem['product_variant_id'] ?? null,
                'product_name' => $cartItem['product_name'] ?? 'Unknown Product',
                'product_sku' => $cartItem['product_sku'] ?? null,
                'product_attributes' => $this->extractProductAttributes($cartItem),
                'quantity' => $cartItem['quantity'],
                'price' => $cartItem['price'],
                'discount_amount' => $cartItem['discount_amount'] ?? 0,
                'tax_amount' => $cartItem['tax_amount'] ?? 0,
                'total' => $cartItem['price'] * $cartItem['quantity'] - ($cartItem['discount_amount'] ?? 0)
            ]);

            Log::debug('Order item created', [
                'order_id' => $order->id,
                'item_id' => $orderItem->id,
                'product_id' => $cartItem['product_id'],
                'quantity' => $cartItem['quantity']
            ]);
        }
    }

    public function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        
        // Ensure uniqueness
        $orderNumber = "{$prefix}-{$date}-{$random}";
        while (Order::where('order_number', $orderNumber)->exists()) {
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            $orderNumber = "{$prefix}-{$date}-{$random}";
        }
        
        return $orderNumber;
    }

    private function extractProductAttributes(array $cartItem): array
    {
        $attributes = [];
        
        // Extract common product variant attributes
        if (isset($cartItem['color'])) {
            $attributes['color'] = $cartItem['color'];
        }
        
        if (isset($cartItem['size'])) {
            $attributes['size'] = $cartItem['size'];
        }
        
        if (isset($cartItem['material'])) {
            $attributes['material'] = $cartItem['material'];
        }
        
        // Include any other variant-specific attributes
        if (isset($cartItem['attributes']) && is_array($cartItem['attributes'])) {
            $attributes = array_merge($attributes, $cartItem['attributes']);
        }
        
        return $attributes;
    }
}