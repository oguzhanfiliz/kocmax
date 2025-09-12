<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartStrategyInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class AuthenticatedCartStrategy implements CartStrategyInterface
{
    /**
     * Sepete ürün ekler. Eğer aynı varyanttan mevcutsa miktarı artırır.
     *
     * @param Cart $cart Sepet modeli
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Eklenecek miktar
     * @return void
     */
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        $existingItem = $cart->items()
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity
            ]);
            
            Log::debug('Updated existing cart item quantity', [
                'cart_id' => $cart->id,
                'item_id' => $existingItem->id,
                'old_quantity' => $existingItem->quantity - $quantity,
                'new_quantity' => $existingItem->quantity
            ]);
        } else {
            $newItem = $cart->items()->create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'price' => $variant->price, // FiyatlandırmaServisi tarafından tekrar hesaplanacaktır
                'discounted_price' => $variant->price
            ]);
            
            Log::debug('Created new cart item', [
                'cart_id' => $cart->id,
                'item_id' => $newItem->id,
                'variant_id' => $variant->id,
                'quantity' => $quantity
            ]);
        }

        $cart->touch(); // Sepet zaman damgasını güncelle
    }

    /**
     * Sepet öğesinin miktarını günceller. Miktar 0 veya altına inerse öğeyi kaldırır.
     *
     * @param Cart $cart Sepet modeli
     * @param CartItem $item Sepet öğesi
     * @param int $quantity Yeni miktar
     * @return void
     */
    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($cart, $item);
            return;
        }

        $oldQuantity = $item->quantity;
        $item->update(['quantity' => $quantity]);
        $cart->touch();
        
        Log::debug('Updated cart item quantity', [
            'cart_id' => $cart->id,
            'item_id' => $item->id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $quantity
        ]);
    }

    /**
     * Sepetten belirli bir öğeyi kaldırır.
     *
     * @param Cart $cart Sepet modeli
     * @param CartItem $item Kaldırılacak öğe
     * @return void
     */
    public function removeItem(Cart $cart, CartItem $item): void
    {
        $itemId = $item->id;
        $item->delete();
        $cart->touch();
        
        Log::debug('Removed cart item', [
            'cart_id' => $cart->id,
            'item_id' => $itemId
        ]);
    }

    /**
     * Sepeti tamamen temizler ve özet alanlarını sıfırlar.
     *
     * @param Cart $cart Sepet modeli
     * @return void
     */
    public function clear(Cart $cart): void
    {
        $itemCount = $cart->items()->count();
        $cart->items()->delete();
        
        $cart->update([
            'total_amount' => 0,
            'discounted_amount' => 0,
            'subtotal_amount' => 0,
            'coupon_code' => null,
            'coupon_discount' => 0,
            'applied_discounts' => null,
            'pricing_calculated_at' => null,
            'last_pricing_update' => null,
            'pricing_context' => null,
        ]);
        
        Log::debug('Cleared cart', [
            'cart_id' => $cart->id,
            'items_removed' => $itemCount
        ]);
    }

    /**
     * Belirli bir işlem için sepet üzerinde doğrulama yapar.
     *
     * @param Cart $cart Sepet modeli
     * @param string $operation İşlem adı (add_item, update_quantity, remove_item, clear)
     * @param array $context İşleme özgü bağlam verileri
     * @return bool Geçerlilik sonucu
     */
    public function validateOperation(Cart $cart, string $operation, array $context = []): bool
    {
        // Kimliği doğrulanmış sepetlerde standart doğrulama uygulanır
        switch ($operation) {
            case 'add_item':
                return $this->validateAddItem($cart, $context);
            case 'update_quantity':
                return $this->validateUpdateQuantity($cart, $context);
            case 'remove_item':
                return $this->validateRemoveItem($cart, $context);
            case 'clear':
                return $this->validateClear($cart, $context);
            default:
                return false;
        }
    }

    /**
     * Öğre ekleme işlemi için temel doğrulamaları yapar.
     *
     * @param Cart $cart Sepet modeli
     * @param array $context Bağlam verileri (variant, quantity)
     * @return bool
     */
    private function validateAddItem(Cart $cart, array $context): bool
    {
        // Kullanıcının kimliği doğrulanmış olmalıdır
        if (!$cart->user_id) {
            return false;
        }

        // Temel doğrulama — daha kapsamlı doğrulama CartValidationService içinde yapılır
        return isset($context['variant']) && isset($context['quantity']) && $context['quantity'] > 0;
    }

    /**
     * Miktar güncelleme işlemi için doğrulama yapar.
     *
     * @param Cart $cart Sepet modeli
     * @param array $context Bağlam verileri (item, quantity)
     * @return bool
     */
    private function validateUpdateQuantity(Cart $cart, array $context): bool
    {
        if (!$cart->user_id) {
            return false;
        }

        return isset($context['item']) && isset($context['quantity']) && $context['quantity'] >= 0;
    }

    /**
     * Öğeyi kaldırma işlemi için doğrulama yapar.
     *
     * @param Cart $cart Sepet modeli
     * @param array $context Bağlam verileri (item)
     * @return bool
     */
    private function validateRemoveItem(Cart $cart, array $context): bool
    {
        if (!$cart->user_id) {
            return false;
        }

        return isset($context['item']) && $context['item']->cart_id === $cart->id;
    }

    /**
     * Sepeti temizleme işlemi için doğrulama yapar.
     *
     * @param Cart $cart Sepet modeli
     * @param array $context Bağlam verileri
     * @return bool
     */
    private function validateClear(Cart $cart, array $context): bool
    {
        return $cart->user_id !== null;
    }
}