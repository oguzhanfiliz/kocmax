<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\ValueObjects\Cart\CheckoutContext;
use App\ValueObjects\Order\OrderValidationResult;

class OrderValidationService
{
    /**
     * Validate order creation data
     */
    public function validateOrderCreation(CheckoutContext $context, array $orderData): OrderValidationResult
    {
        $errors = [];
        $warnings = [];

        // Validate checkout context
        if ($context->isEmpty()) {
            $errors[] = 'Cart cannot be empty for order creation';
        }

        // Validate required order data
        $requiredFields = ['shipping_address', 'billing_address'];
        foreach ($requiredFields as $field) {
            if (!isset($orderData[$field]) || empty($orderData[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        // Validate addresses
        if (isset($orderData['shipping_address'])) {
            $shippingValidation = $this->validateAddress($orderData['shipping_address'], 'shipping');
            $errors = array_merge($errors, $shippingValidation);
        }

        if (isset($orderData['billing_address'])) {
            $billingValidation = $this->validateAddress($orderData['billing_address'], 'billing');
            $errors = array_merge($errors, $billingValidation);
        }

        // Validate payment method if provided
        if (isset($orderData['payment_method'])) {
            $paymentValidation = $this->validatePaymentMethod($orderData['payment_method']);
            $errors = array_merge($errors, $paymentValidation);
        }

        // Business-specific validations
        if ($context->isB2B()) {
            $b2bValidation = $this->validateB2BOrder($context, $orderData);
            $errors = array_merge($errors, $b2bValidation);
        }

        // Generate warnings
        $warnings = $this->generateOrderWarnings($context, $orderData);

        return empty($errors) 
            ? OrderValidationResult::valid($warnings)
            : OrderValidationResult::invalid($errors, $warnings);
    }

    /**
     * Validate existing order
     */
    public function validateOrder(Order $order): OrderValidationResult
    {
        $errors = [];
        $warnings = [];

        // Validate order integrity
        if ($order->items()->count() === 0) {
            $errors[] = 'Order has no items';
        }

        // Validate order amounts
        if ($order->total_amount <= 0) {
            $errors[] = 'Order total amount must be greater than zero';
        }

        // Validate order status consistency
        $statusValidation = $this->validateOrderStatus($order);
        $errors = array_merge($errors, $statusValidation);

        return empty($errors) 
            ? OrderValidationResult::valid($warnings)
            : OrderValidationResult::invalid($errors, $warnings);
    }

    /**
     * Validate address data
     */
    private function validateAddress(array $address, string $type): array
    {
        $errors = [];
        $requiredFields = ['first_name', 'last_name', 'address_line_1', 'city', 'country'];

        foreach ($requiredFields as $field) {
            if (!isset($address[$field]) || empty(trim($address[$field]))) {
                $errors[] = "Missing required {$type} address field: {$field}";
            }
        }

        // Validate email for billing address
        if ($type === 'billing' && isset($address['email'])) {
            if (!filter_var($address['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format in billing address';
            }
        }

        return $errors;
    }

    /**
     * Validate payment method
     */
    private function validatePaymentMethod(string $paymentMethod): array
    {
        $errors = [];
        $allowedMethods = ['credit_card', 'bank_transfer', 'cash_on_delivery', 'paypal'];

        if (!in_array($paymentMethod, $allowedMethods)) {
            $errors[] = "Invalid payment method: {$paymentMethod}";
        }

        return $errors;
    }

    /**
     * Validate B2B specific requirements
     */
    private function validateB2BOrder(CheckoutContext $context, array $orderData): array
    {
        $errors = [];

        // B2B orders might require tax ID
        if (!isset($orderData['tax_id']) || empty($orderData['tax_id'])) {
            $errors[] = 'Tax ID is required for B2B orders';
        }

        // B2B orders might have minimum order amount
        if ($context->getTotal() < 100) { // Example minimum
            $errors[] = 'B2B orders must have minimum amount of 100';
        }

        return $errors;
    }

    /**
     * Validate order status consistency
     */
    private function validateOrderStatus(Order $order): array
    {
        $errors = [];

        // Check status transitions
        if ($order->status === 'shipped' && !$order->shipped_at) {
            $errors[] = 'Order marked as shipped but missing shipped_at timestamp';
        }

        if ($order->status === 'delivered' && !$order->delivered_at) {
            $errors[] = 'Order marked as delivered but missing delivered_at timestamp';
        }

        return $errors;
    }

    /**
     * Generate warnings for potential issues
     */
    private function generateOrderWarnings(CheckoutContext $context, array $orderData): array
    {
        $warnings = [];

        // Warning for high-value orders
        if ($context->getTotal() > 1000) {
            $warnings[] = 'High-value order - consider additional verification';
        }

        // Warning for international shipping
        if (isset($orderData['shipping_address']['country']) && 
            $orderData['shipping_address']['country'] !== 'TR') {
            $warnings[] = 'International shipping may require additional documentation';
        }

        return $warnings;
    }
}
