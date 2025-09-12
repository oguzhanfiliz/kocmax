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
    /**
     * Servis kurucusu.
     *
     * @param CustomerTypeDetector $customerTypeDetector Müşteri tipi belirleyici
     */
    public function __construct(
        private CustomerTypeDetector $customerTypeDetector
    ) {}

    /**
     * Sepete öğe ekleme için doğrulama yapar.
     *
     * @param Cart $cart Sepet
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Miktar
     * @return CartValidationResult Doğrulama sonucu
     */
    public function validateAddItem(Cart $cart, ProductVariant $variant, int $quantity): CartValidationResult
    {
        $errors = [];

        // Temel miktar doğrulaması
        if ($quantity <= 0) {
            $errors[] = "Quantity must be greater than 0";
        }

        if ($quantity > 999) {
            $errors[] = "Maximum quantity per item is 999";
        }

        // Stok doğrulaması
        if ($variant->stock < $quantity) {
            $errors[] = "Insufficient stock. Available: {$variant->stock}, Requested: {$quantity}";
        }

        // Ürün uygunluk doğrulaması
        if (!$variant->product->is_active) {
            $errors[] = "Product is not available";
        }

        // Öğenin sepette zaten bulunup bulunmadığını kontrol et
        $existingItem = $cart->items()
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($existingItem) {
            $totalQuantity = $existingItem->quantity + $quantity;
            if ($totalQuantity > $variant->stock) {
                $errors[] = "Total quantity ({$totalQuantity}) exceeds available stock ({$variant->stock})";
            }
        }

        // B2B'ye özgü doğrulamalar
        if ($cart->user && $cart->user->isDealer()) {
            $b2bValidation = $this->validateB2BConstraints($cart, $variant, $quantity);
            $errors = array_merge($errors, $b2bValidation);
        }

        return new CartValidationResult(
            isValid: empty($errors),
            errors: $errors
        );
    }

    /**
     * Miktar güncelleme işlemi için doğrulama yapar.
     *
     * @param CartItem $item Sepet öğesi
     * @param int $newQuantity Yeni miktar
     * @return CartValidationResult Doğrulama sonucu
     */
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

    /**
     * Ödeme (checkout) için sepeti doğrular.
     *
     * @param Cart $cart Sepet
     * @return CartValidationResult Doğrulama sonucu
     */
    public function validateForCheckout(Cart $cart): CartValidationResult
    {
        $errors = [];

        // Boş sepet kontrolü
        if ($cart->items->isEmpty()) {
            $errors[] = "Cart is empty";
        }

        // Her bir öğeyi doğrula
        foreach ($cart->items as $item) {
            $itemValidation = $this->validateCartItem($item);
            if (!$itemValidation->isValid()) {
                $errors = array_merge($errors, $itemValidation->getErrors());
            }
        }

        // B2B'ye özgü ödeme doğrulamaları
        if ($cart->user && $cart->user->isDealer()) {
            $b2bCheckoutValidation = $this->validateB2BCheckout($cart);
            $errors = array_merge($errors, $b2bCheckoutValidation);
        }

        // Minimum sipariş tutarı doğrulaması
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

    /**
     * Tek bir sepet öğesi için doğrulama yapar.
     *
     * @param CartItem $item Sepet öğesi
     * @return CartValidationResult Doğrulama sonucu
     */
    private function validateCartItem(CartItem $item): CartValidationResult
    {
        $errors = [];

        // Ürün uygunluğu
        if (!$item->product->is_active) {
            $errors[] = "Product '{$item->product->name}' is no longer available";
        }

        // Varyant uygunluğu
        if (!$item->productVariant->product->is_active) {
            $errors[] = "Product variant is no longer available";
        }

        // Stok uygunluğu
        if ($item->quantity > $item->productVariant->stock) {
            $errors[] = "Insufficient stock for '{$item->product->name}'. Available: {$item->productVariant->stock}";
        }

        // Fiyat doğrulaması (eski fiyat tespit etme)
        if ($item->price_calculated_at && $item->price_calculated_at->lt(now()->subHours(24))) {
            $errors[] = "Price information is outdated for '{$item->product->name}'";
        }

        return new CartValidationResult(
            isValid: empty($errors),
            errors: $errors
        );
    }

    /**
     * B2B kısıtlarını doğrular.
     *
     * @param Cart $cart Sepet
     * @param ProductVariant $variant Varyant
     * @param int $quantity Miktar
     * @return array Hata mesajları
     */
    private function validateB2BConstraints(Cart $cart, ProductVariant $variant, int $quantity): array
    {
        $errors = [];

        // Kredi limiti doğrulaması burada yapılabilir
        // Bunun için bir kredi yönetim sistemi entegrasyonu gerekir
        
        // B2B için minimum adet (varsa)
        if ($variant->product->min_b2b_quantity && $quantity < $variant->product->min_b2b_quantity) {
            $errors[] = "Minimum B2B quantity for this product is {$variant->product->min_b2b_quantity}";
        }

        return $errors;
    }

    /**
     * B2B ödeme (checkout) doğrulamaları.
     *
     * @param Cart $cart Sepet
     * @return array Hata mesajları
     */
    private function validateB2BCheckout(Cart $cart): array
    {
        $errors = [];

        // Bayi onay kontrolü
        if (!$cart->user->is_approved_dealer) {
            $errors[] = "Dealer account is not approved for checkout";
        }

        // Kredi limiti doğrulaması
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