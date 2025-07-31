<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            // Shipping Information
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_country' => 'required|string|max:2',
            
            // Billing Information
            'billing_same_as_shipping' => 'boolean',
            'billing_name' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'billing_email' => 'required_if:billing_same_as_shipping,false|nullable|email|max:255',
            'billing_phone' => 'nullable|string|max:20',
            'billing_address' => 'required_if:billing_same_as_shipping,false|nullable|string|max:500',
            'billing_city' => 'required_if:billing_same_as_shipping,false|nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_country' => 'required_if:billing_same_as_shipping,false|nullable|string|max:2',
            'billing_tax_number' => 'nullable|string|max:50',
            'billing_tax_office' => 'nullable|string|max:255',
            
            // Payment Information
            'payment_method' => 'required|string|in:card,credit,bank_transfer',
            'payment_data' => 'required|array',
            
            // Card Payment Data
            'payment_data.card_number' => 'required_if:payment_method,card|nullable|string',
            'payment_data.card_expiry' => 'required_if:payment_method,card|nullable|string',
            'payment_data.card_cvv' => 'required_if:payment_method,card|nullable|string',
            'payment_data.cardholder_name' => 'required_if:payment_method,card|nullable|string|max:255',
            
            // Bank Transfer Data
            'payment_data.bank_reference' => 'nullable|string|max:255',
            
            // Additional Information
            'notes' => 'nullable|string|max:1000',
            'shipping_method' => 'nullable|string|max:100',
            
            // Guest Checkout Data (for guest checkout endpoint)
            'cart_data' => 'sometimes|array',
            'cart_data.items' => 'required_with:cart_data|array',
            'cart_data.items.*.product_id' => 'required_with:cart_data.items|integer|exists:products,id',
            'cart_data.items.*.product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'cart_data.items.*.quantity' => 'required_with:cart_data.items|integer|min:1',
            'cart_data.items.*.price' => 'required_with:cart_data.items|numeric|min:0',
            'cart_data.items.*.discounted_price' => 'nullable|numeric|min:0',
            'cart_data.total_amount' => 'required_with:cart_data|numeric|min:0',
            'cart_data.discounted_amount' => 'nullable|numeric|min:0',
            'cart_data.subtotal_amount' => 'required_with:cart_data|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_name.required' => 'Shipping name is required',
            'shipping_email.required' => 'Shipping email is required',
            'shipping_email.email' => 'Please provide a valid shipping email address',
            'shipping_address.required' => 'Shipping address is required',
            'shipping_city.required' => 'Shipping city is required',
            'shipping_country.required' => 'Shipping country is required',
            
            'billing_name.required_if' => 'Billing name is required when billing address differs from shipping',
            'billing_email.required_if' => 'Billing email is required when billing address differs from shipping',
            'billing_address.required_if' => 'Billing address is required when different from shipping address',
            'billing_city.required_if' => 'Billing city is required when billing address differs from shipping',
            'billing_country.required_if' => 'Billing country is required when billing address differs from shipping',
            
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Payment method must be one of: card, credit, bank_transfer',
            
            'payment_data.card_number.required_if' => 'Card number is required for card payments',
            'payment_data.card_expiry.required_if' => 'Card expiry is required for card payments',
            'payment_data.card_cvv.required_if' => 'Card CVV is required for card payments',
            'payment_data.cardholder_name.required_if' => 'Cardholder name is required for card payments',
            
            'cart_data.items.required_with' => 'Cart items are required for guest checkout',
            'cart_data.items.*.product_id.required_with' => 'Product ID is required for each cart item',
            'cart_data.items.*.product_id.exists' => 'One or more products in cart do not exist',
            'cart_data.items.*.quantity.required_with' => 'Quantity is required for each cart item',
            'cart_data.items.*.quantity.min' => 'Quantity must be at least 1',
            'cart_data.items.*.price.required_with' => 'Price is required for each cart item',
            'cart_data.total_amount.required_with' => 'Total amount is required for guest checkout',
            'cart_data.subtotal_amount.required_with' => 'Subtotal amount is required for guest checkout'
        ];
    }

    protected function prepareForValidation(): void
    {
        // If billing is same as shipping, copy shipping data to billing
        if ($this->input('billing_same_as_shipping', false)) {
            $this->merge([
                'billing_name' => $this->input('shipping_name'),
                'billing_email' => $this->input('shipping_email'),
                'billing_phone' => $this->input('shipping_phone'),
                'billing_address' => $this->input('shipping_address'),
                'billing_city' => $this->input('shipping_city'),
                'billing_state' => $this->input('shipping_state'),
                'billing_postal_code' => $this->input('shipping_postal_code'),
                'billing_country' => $this->input('shipping_country')
            ]);
        }
    }
}