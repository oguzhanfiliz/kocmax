<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\ValueObjects\Cart\CheckoutContext;
use App\ValueObjects\Order\OrderValidationResult;

class OrderValidationService
{
    /**
     * Sipariş oluşturma verilerini doğrular.
     */
    public function validateOrderCreation(CheckoutContext $context, array $orderData): OrderValidationResult
    {
        $errors = [];
        $warnings = [];

        // Checkout bağlamını doğrula
        if ($context->isEmpty()) {
            $errors[] = 'Cart cannot be empty for order creation';
        }

        // Gerekli sipariş verilerini doğrula
        $requiredFields = ['shipping_address', 'billing_address'];
        foreach ($requiredFields as $field) {
            if (!isset($orderData[$field]) || empty($orderData[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        // Adresleri doğrula
        if (isset($orderData['shipping_address'])) {
            $shippingValidation = $this->validateAddress($orderData['shipping_address'], 'shipping');
            $errors = array_merge($errors, $shippingValidation);
        }

        if (isset($orderData['billing_address'])) {
            $billingValidation = $this->validateAddress($orderData['billing_address'], 'billing');
            $errors = array_merge($errors, $billingValidation);
        }

        // Ödeme yöntemi verildiyse doğrula
        if (isset($orderData['payment_method'])) {
            $paymentValidation = $this->validatePaymentMethod($orderData['payment_method']);
            $errors = array_merge($errors, $paymentValidation);
        }

        // İşe özgü doğrulamalar
        if ($context->isB2B()) {
            $b2bValidation = $this->validateB2BOrder($context, $orderData);
            $errors = array_merge($errors, $b2bValidation);
        }

        // Uyarıları oluştur
        $warnings = $this->generateOrderWarnings($context, $orderData);

        return empty($errors) 
            ? OrderValidationResult::valid($warnings)
            : OrderValidationResult::invalid($errors, $warnings);
    }

    /**
     * Mevcut siparişi doğrular.
     */
    public function validateOrder(Order $order): OrderValidationResult
    {
        $errors = [];
        $warnings = [];

        // Sipariş bütünlüğünü doğrula
        if ($order->items()->count() === 0) {
            $errors[] = 'Order has no items';
        }

        // Sipariş tutarlarını doğrula
        if ($order->total_amount <= 0) {
            $errors[] = 'Order total amount must be greater than zero';
        }

        // Sipariş durum tutarlılığını doğrula
        $statusValidation = $this->validateOrderStatus($order);
        $errors = array_merge($errors, $statusValidation);

        return empty($errors) 
            ? OrderValidationResult::valid($warnings)
            : OrderValidationResult::invalid($errors, $warnings);
    }

    /**
     * Adres verilerini doğrular.
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

        // Fatura adresi için e-posta doğrulaması
        if ($type === 'billing' && isset($address['email'])) {
            if (!filter_var($address['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format in billing address';
            }
        }

        return $errors;
    }

    /**
     * Ödeme yöntemini doğrular.
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
     * B2B'ye özel gereklilikleri doğrular.
     */
    private function validateB2BOrder(CheckoutContext $context, array $orderData): array
    {
        $errors = [];

        // B2B siparişleri vergi numarası gerektirebilir
        if (!isset($orderData['tax_id']) || empty($orderData['tax_id'])) {
            $errors[] = 'Tax ID is required for B2B orders';
        }

        // B2B siparişlerinde minimum sipariş tutarı olabilir
        if ($context->getTotal() < 100) { // Example minimum
            $errors[] = 'B2B orders must have minimum amount of 100';
        }

        return $errors;
    }

    /**
     * Sipariş durum tutarlılığını doğrular.
     */
    private function validateOrderStatus(Order $order): array
    {
        $errors = [];

        // Durum geçişlerini kontrol et
        if ($order->status === 'shipped' && !$order->shipped_at) {
            $errors[] = 'Order marked as shipped but missing shipped_at timestamp';
        }

        if ($order->status === 'delivered' && !$order->delivered_at) {
            $errors[] = 'Order marked as delivered but missing delivered_at timestamp';
        }

        return $errors;
    }

    /**
     * Potansiyel sorunlar için uyarılar üretir.
     */
    private function generateOrderWarnings(CheckoutContext $context, array $orderData): array
    {
        $warnings = [];

        // Yüksek tutarlı siparişler için uyarı
        if ($context->getTotal() > 1000) {
            $warnings[] = 'High-value order - consider additional verification';
        }

        // Uluslararası gönderiler için uyarı
        if (isset($orderData['shipping_address']['country']) && 
            $orderData['shipping_address']['country'] !== 'TR') {
            $warnings[] = 'International shipping may require additional documentation';
        }

        return $warnings;
    }
}
