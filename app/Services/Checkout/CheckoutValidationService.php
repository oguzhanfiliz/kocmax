<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\ValueObjects\Cart\CheckoutContext;
use App\ValueObjects\Order\OrderValidationResult;

class CheckoutValidationService
{
    public function validateCheckout(CheckoutContext $context, array $checkoutData): OrderValidationResult
    {
        $errors = [];
        $warnings = [];

        // Validate checkout context
        $contextValidation = $this->validateCheckoutContext($context);
        $errors = array_merge($errors, $contextValidation);

        // Validate shipping information
        $shippingValidation = $this->validateShippingInfo($checkoutData);
        $errors = array_merge($errors, $shippingValidation);

        // Validate billing information
        $billingValidation = $this->validateBillingInfo($checkoutData);
        $errors = array_merge($errors, $billingValidation);

        // Validate payment information
        $paymentValidation = $this->validatePaymentInfo($checkoutData);
        $errors = array_merge($errors, $paymentValidation);

        // Business-specific validations
        if ($context->isB2B()) {
            $b2bValidation = $this->validateB2BCheckout($context, $checkoutData);
            $errors = array_merge($errors, $b2bValidation);
        }

        // Generate warnings for potential issues
        $warnings = $this->generateCheckoutWarnings($context, $checkoutData);

        return empty($errors) 
            ? OrderValidationResult::valid($warnings)
            : OrderValidationResult::invalid($errors, $warnings);
    }

    private function validateCheckoutContext(CheckoutContext $context): array
    {
        $errors = [];

        // Validate cart is not empty
        if ($context->isEmpty()) {
            $errors[] = 'Cart is empty';
        }

        // Validate total amount
        if ($context->getTotalAmount() <= 0) {
            $errors[] = 'Invalid total amount';
        }

        // Validate items
        foreach ($context->getItems() as $item) {
            if (empty($item['product_id'])) {
                $errors[] = 'Invalid product in cart';
            }
            
            if (empty($item['quantity']) || $item['quantity'] <= 0) {
                $errors[] = 'Invalid quantity for product: ' . ($item['product_name'] ?? 'Unknown');
            }
        }

        return $errors;
    }

    private function validateShippingInfo(array $checkoutData): array
    {
        $errors = [];
        $requiredFields = [
            'shipping_name' => 'Shipping name',
            'shipping_address' => 'Shipping address',
            'shipping_city' => 'Shipping city',
            'shipping_country' => 'Shipping country'
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($checkoutData[$field])) {
                $errors[] = "{$label} is required";
            }
        }

        // Validate email if provided
        if (!empty($checkoutData['shipping_email'])) {
            if (!filter_var($checkoutData['shipping_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid shipping email address';
            }
        }

        // Validate phone if provided
        if (!empty($checkoutData['shipping_phone'])) {
            if (!$this->validatePhoneNumber($checkoutData['shipping_phone'])) {
                $errors[] = 'Invalid shipping phone number';
            }
        }

        return $errors;
    }

    private function validateBillingInfo(array $checkoutData): array
    {
        $errors = [];
        
        // If billing is same as shipping, skip validation
        if (!empty($checkoutData['billing_same_as_shipping'])) {
            return $errors;
        }

        $requiredFields = [
            'billing_name' => 'Billing name',
            'billing_address' => 'Billing address',
            'billing_city' => 'Billing city',
            'billing_country' => 'Billing country'
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($checkoutData[$field])) {
                $errors[] = "{$label} is required";
            }
        }

        // Validate billing email
        if (!empty($checkoutData['billing_email'])) {
            if (!filter_var($checkoutData['billing_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid billing email address';
            }
        }

        return $errors;
    }

    private function validatePaymentInfo(array $checkoutData): array
    {
        $errors = [];
        $paymentMethod = $checkoutData['payment_method'] ?? 'card';

        switch ($paymentMethod) {
            case 'card':
                $errors = array_merge($errors, $this->validateCardPayment($checkoutData));
                break;
            case 'credit':
                $errors = array_merge($errors, $this->validateCreditPayment($checkoutData));
                break;
            case 'bank_transfer':
                $errors = array_merge($errors, $this->validateBankTransferPayment($checkoutData));
                break;
            default:
                $errors[] = "Unsupported payment method: {$paymentMethod}";
        }

        return $errors;
    }

    private function validateCardPayment(array $checkoutData): array
    {
        $errors = [];
        $paymentData = $checkoutData['payment_data'] ?? [];

        $requiredFields = [
            'card_number' => 'Card number',
            'card_expiry' => 'Card expiry date',
            'card_cvv' => 'Card CVV',
            'cardholder_name' => 'Cardholder name'
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($paymentData[$field])) {
                $errors[] = "{$label} is required";
            }
        }

        // Validate card number format
        if (!empty($paymentData['card_number'])) {
            $cardNumber = preg_replace('/\s+/', '', $paymentData['card_number']);
            if (!$this->validateCardNumber($cardNumber)) {
                $errors[] = 'Invalid card number';
            }
        }

        // Validate expiry date
        if (!empty($paymentData['card_expiry'])) {
            if (!$this->validateCardExpiry($paymentData['card_expiry'])) {
                $errors[] = 'Invalid or expired card';
            }
        }

        // Validate CVV
        if (!empty($paymentData['card_cvv'])) {
            if (!preg_match('/^\d{3,4}$/', $paymentData['card_cvv'])) {
                $errors[] = 'Invalid CVV';
            }
        }

        return $errors;
    }

    private function validateCreditPayment(array $checkoutData): array
    {
        $errors = [];

        // Credit payment is only available for B2B customers
        if (empty($checkoutData['user_id'])) {
            $errors[] = 'Credit payment requires authenticated user';
        }

        // Additional B2B credit validations would go here
        // This would typically validate credit limits, approval status, etc.

        return $errors;
    }

    private function validateBankTransferPayment(array $checkoutData): array
    {
        $errors = [];

        // Bank transfer might require additional information
        $paymentData = $checkoutData['payment_data'] ?? [];
        
        if (empty($paymentData['bank_reference'])) {
            // Bank reference might be optional for some bank transfer methods
        }

        return $errors;
    }

    private function validateB2BCheckout(CheckoutContext $context, array $checkoutData): array
    {
        $errors = [];

        // Validate tax information for B2B
        if (empty($checkoutData['billing_tax_number'])) {
            $errors[] = 'Tax number is required for business orders';
        }

        if (empty($checkoutData['billing_tax_office'])) {
            $errors[] = 'Tax office is required for business orders';
        }

        // Validate minimum order amount
        $minOrderAmount = 100.0; // This could come from configuration
        if ($context->getTotalAmount() < $minOrderAmount) {
            $errors[] = "Minimum order amount for B2B is {$minOrderAmount} TL";
        }

        return $errors;
    }

    private function generateCheckoutWarnings(CheckoutContext $context, array $checkoutData): array
    {
        $warnings = [];

        // Warn about high shipping costs
        $shippingCountry = $checkoutData['shipping_country'] ?? 'TR';
        if ($shippingCountry !== 'TR') {
            $warnings[] = 'International shipping may take 7-14 business days';
        }

        // Warn about payment method
        $paymentMethod = $checkoutData['payment_method'] ?? 'card';
        if ($paymentMethod === 'bank_transfer') {
            $warnings[] = 'Bank transfer payments require manual verification and may delay order processing';
        }

        // Warn about large orders
        if ($context->getTotalAmount() > 5000) {
            $warnings[] = 'Large orders may require additional verification';
        }

        return $warnings;
    }

    private function validatePhoneNumber(string $phone): bool
    {
        // Basic phone number validation
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        return strlen($cleaned) >= 10 && strlen($cleaned) <= 15;
    }

    private function validateCardNumber(string $cardNumber): bool
    {
        // Basic Luhn algorithm validation
        if (!preg_match('/^\d{13,19}$/', $cardNumber)) {
            return false;
        }

        $sum = 0;
        $length = strlen($cardNumber);
        
        for ($i = 0; $i < $length; $i++) {
            $digit = intval($cardNumber[$length - 1 - $i]);
            
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }
        
        return ($sum % 10) === 0;
    }

    private function validateCardExpiry(string $expiry): bool
    {
        // Validate MM/YY or MM/YYYY format
        if (!preg_match('/^(\d{2})\/(\d{2,4})$/', $expiry, $matches)) {
            return false;
        }

        $month = intval($matches[1]);
        $year = intval($matches[2]);
        
        // Convert YY to YYYY
        if ($year < 100) {
            $year += 2000;
        }
        
        // Validate month
        if ($month < 1 || $month > 12) {
            return false;
        }
        
        // Check if card is expired
        $expiryDate = mktime(0, 0, 0, $month + 1, 1, $year); // First day of next month
        return $expiryDate > time();
    }
}