<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartStrategyInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class GuestCartStrategy implements CartStrategyInterface
{
    /**
     * Misafir sepetine ürün ekler: önce oturumda saklar, ardından veritabanıyla senkronize eder.
     *
     * @param Cart $cart Sepet
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Miktar
     * @return void
     */
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        // Önce oturumda saklamayı ele al
        $this->addItemToSession($cart, $variant, $quantity);
        
        // Ardından veritabanı ile senkronize et
        $this->syncCartFromSession($cart);
    }

    /**
     * Misafir sepetindeki bir öğenin miktarını günceller. Gerekirse siler.
     *
     * @param Cart $cart Sepet
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

        // Oturumu güncelle
        $this->updateQuantityInSession($cart, $item, $quantity);
        
        // Veritabanı ile senkronize et
        $this->syncCartFromSession($cart);
    }

    /**
     * Misafir sepetinden öğe kaldırır: önce oturumdan, sonra veritabanıyla senkronize eder.
     *
     * @param Cart $cart Sepet
     * @param CartItem $item Kaldırılacak öğe
     * @return void
     */
    public function removeItem(Cart $cart, CartItem $item): void
    {
        // Oturumdan kaldır
        $this->removeItemFromSession($cart, $item);
        
        // Veritabanı ile senkronize et
        $this->syncCartFromSession($cart);
    }

    /**
     * Misafir sepetini tamamen temizler ve özet alanlarını sıfırlar.
     *
     * @param Cart $cart Sepet
     * @return void
     */
    public function clear(Cart $cart): void
    {
        $sessionKey = $this->getSessionKey($cart);
        Session::forget($sessionKey);
        
        // Veritabanını temizle
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
        
        Log::debug('Cleared guest cart', [
            'cart_id' => $cart->id,
            'session_id' => $cart->session_id
        ]);
    }

    /**
     * Misafir sepeti için verilen işlemin geçerli olup olmadığını kontrol eder.
     *
     * @param Cart $cart Sepet
     * @param string $operation İşlem adı (add_item, update_quantity, remove_item, clear)
     * @param array $context İşlem bağlamı
     * @return bool
     */
    public function validateOperation(Cart $cart, string $operation, array $context = []): bool
    {
        // Misafir sepetlerinde session_id olmalı ve user_id olmamalı
        if (!$cart->session_id || $cart->user_id) {
            return false;
        }

        switch ($operation) {
            case 'add_item':
                return $this->validateAddItem($cart, $context);
            case 'update_quantity':
                return $this->validateUpdateQuantity($cart, $context);
            case 'remove_item':
                return $this->validateRemoveItem($cart, $context);
            case 'clear':
                return true; // Misafir sepetleri her zaman temizlenebilir
            default:
                return false;
        }
    }

    /**
     * Öğeyi oturumdaki sepet verisine ekler.
     *
     * @param Cart $cart Sepet
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Miktar
     * @return void
     */
    private function addItemToSession(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        $sessionKey = $this->getSessionKey($cart);
        $cartData = Session::get($sessionKey, ['items' => []]);

        $existingItemKey = $this->findExistingItemInSession($cartData['items'], $variant->id);
        
        if ($existingItemKey !== null) {
            $cartData['items'][$existingItemKey]['quantity'] += $quantity;
            $cartData['items'][$existingItemKey]['updated_at'] = now()->toISOString();
        } else {
            $cartData['items'][] = [
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'price' => $variant->price,
                'added_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];
        }

        $cartData['updated_at'] = now()->toISOString();
        Session::put($sessionKey, $cartData);
        
        Log::debug('Added item to guest cart session', [
            'cart_id' => $cart->id,
            'session_id' => $cart->session_id,
            'variant_id' => $variant->id,
            'quantity' => $quantity
        ]);
    }

    /**
     * Oturumdaki sepet verisinde belirtilen öğenin miktarını günceller.
     *
     * @param Cart $cart Sepet
     * @param CartItem $item Sepet öğesi
     * @param int $quantity Yeni miktar
     * @return void
     */
    private function updateQuantityInSession(Cart $cart, CartItem $item, int $quantity): void
    {
        $sessionKey = $this->getSessionKey($cart);
        $cartData = Session::get($sessionKey, ['items' => []]);

        $itemKey = $this->findExistingItemInSession($cartData['items'], $item->product_variant_id);
        
        if ($itemKey !== null) {
            $cartData['items'][$itemKey]['quantity'] = $quantity;
            $cartData['items'][$itemKey]['updated_at'] = now()->toISOString();
            $cartData['updated_at'] = now()->toISOString();
            
            Session::put($sessionKey, $cartData);
        }
    }

    /**
     * Oturumdaki sepet verisinden ilgili öğeyi kaldırır.
     *
     * @param Cart $cart Sepet
     * @param CartItem $item Kaldırılacak öğe
     * @return void
     */
    private function removeItemFromSession(Cart $cart, CartItem $item): void
    {
        $sessionKey = $this->getSessionKey($cart);
        $cartData = Session::get($sessionKey, ['items' => []]);

        $cartData['items'] = array_values(array_filter($cartData['items'], function ($sessionItem) use ($item) {
            return $sessionItem['product_variant_id'] !== $item->product_variant_id;
        }));

        $cartData['updated_at'] = now()->toISOString();
        Session::put($sessionKey, $cartData);
    }

    /**
     * Oturumdaki sepet verisini veritabanıyla senkronize eder.
     *
     * @param Cart $cart Sepet
     * @return void
     */
    private function syncCartFromSession(Cart $cart): void
    {
        $sessionKey = $this->getSessionKey($cart);
        $cartData = Session::get($sessionKey, ['items' => []]);

        // Mevcut sepet öğelerini temizle
        $cart->items()->delete();

        // Oturumdan yeniden oluştur
        foreach ($cartData['items'] as $sessionItem) {
            $cart->items()->create([
                'product_id' => $sessionItem['product_id'],
                'product_variant_id' => $sessionItem['product_variant_id'],
                'quantity' => $sessionItem['quantity'],
                'price' => $sessionItem['price']
            ]);
        }

        $cart->touch();
        
        Log::debug('Synced guest cart from session to database', [
            'cart_id' => $cart->id,
            'session_id' => $cart->session_id,
            'item_count' => count($cartData['items'])
        ]);
    }

    /**
     * Oturum verisinde aynı varyantı arar ve bulunduğunda indeksini döndürür.
     *
     * @param array $items Oturumdaki öğeler
     * @param int $variantId Varyant kimliği
     * @return int|null Bulunursa indeks, yoksa null
     */
    private function findExistingItemInSession(array $items, int $variantId): ?int
    {
        foreach ($items as $index => $item) {
            if ($item['product_variant_id'] === $variantId) {
                return $index;
            }
        }
        
        return null;
    }

    /**
     * Bu misafir sepeti için oturum anahtarını döndürür.
     *
     * @param Cart $cart Sepet
     * @return string Oturum anahtarı
     */
    private function getSessionKey(Cart $cart): string
    {
        return "guest_cart_{$cart->session_id}";
    }

    /**
     * Ekleme işlemi için temel doğrulama.
     *
     * @param Cart $cart Sepet
     * @param array $context Bağlam (variant, quantity)
     * @return bool
     */
    private function validateAddItem(Cart $cart, array $context): bool
    {
        return isset($context['variant']) && isset($context['quantity']) && $context['quantity'] > 0;
    }

    /**
     * Miktar güncelleme işlemi için temel doğrulama.
     *
     * @param Cart $cart Sepet
     * @param array $context Bağlam (item, quantity)
     * @return bool
     */
    private function validateUpdateQuantity(Cart $cart, array $context): bool
    {
        return isset($context['item']) && isset($context['quantity']) && $context['quantity'] >= 0;
    }

    /**
     * Öğeyi kaldırma işlemi için temel doğrulama.
     *
     * @param Cart $cart Sepet
     * @param array $context Bağlam (item)
     * @return bool
     */
    private function validateRemoveItem(Cart $cart, array $context): bool
    {
        return isset($context['item']) && $context['item']->cart_id === $cart->id;
    }
}