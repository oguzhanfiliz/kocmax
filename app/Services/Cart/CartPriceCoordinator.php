<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Services\PricingService;
use App\Services\Pricing\CustomerTypeDetector;
use App\ValueObjects\Cart\CartSummary;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CartPriceCoordinator
{
    /**
     * Fiyatlandırma servislerini koordine eden sınıfın kurucusu.
     *
     * @param PricingService $pricingService Fiyat hesaplama servisi
     * @param CustomerTypeDetector $customerTypeDetector Müşteri tipi belirleyici
     */
    public function __construct(
        private PricingService $pricingService,
        private CustomerTypeDetector $customerTypeDetector
    ) {}

    /**
     * Verilen sepetin fiyatlandırmasını günceller ve özet bilgiyi döner.
     *
     * @param Cart $cart Sepet
     * @return CartSummary Sepet özeti
     * @throws \Exception Hesaplama sırasında hata oluşursa aynen fırlatılır
     */
    public function updateCartPricing(Cart $cart): CartSummary
    {
        try {
            // Müşteri tipi tespitini güncelle
            $customerType = $this->customerTypeDetector->detect($cart->user);
            
            // Tek tek ürün kalemlerinin fiyatlarını güncelle
            $this->updateAllItemPrices($cart);
            
            // Sepet özetini hesapla
            $summary = $this->calculateCartSummary($cart);
            
            // Hesaplanan değerlerle sepeti güncelle
            $cart->update([
                'customer_type' => $customerType->value,
                'subtotal_amount' => $summary->getSubtotal(),
                'total_amount' => $summary->getTotal(),
                'discounted_amount' => $summary->getTotal(),
                'pricing_calculated_at' => now(),
                'last_pricing_update' => now(),
                'applied_discounts' => $summary->getAppliedDiscounts(),
            ]);

            Log::info('Cart pricing updated successfully', [
                'cart_id' => $cart->id,
                'customer_type' => $customerType->value,
                'total_amount' => $summary->getTotal()
            ]);

            return $summary;

        } catch (\Exception $e) {
            Log::error('Failed to update cart pricing', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sepet özetini hesaplar ve önbelleğe alır.
     *
     * @param Cart $cart Sepet
     * @return CartSummary Hesaplanan özet
     */
    public function calculateCartSummary(Cart $cart): CartSummary
    {
        $cacheKey = "cart_summary_{$cart->id}_{$cart->updated_at->timestamp}";
        
        return Cache::remember($cacheKey, 300, function () use ($cart) {
            return $this->performCartSummaryCalculation($cart);
        });
    }

    /**
     * Sepetteki tüm fiyatları tazeler, özet ve önbelleği günceller.
     *
     * @param Cart $cart Sepet
     * @return void
     */
    public function refreshAllPrices(Cart $cart): void
    {
        $this->updateAllItemPrices($cart);
        $this->updateCartPricing($cart);
        
        // Bu sepete ait önbelleği temizle
        $cacheKey = "cart_summary_{$cart->id}_*";
        Cache::flush(); // Üretimde daha hedefli bir önbellek geçersizleştirme kullanılmalıdır
    }

    /**
     * Tek bir sepet öğesinin fiyatını hesaplar.
     *
     * @param CartItem $item Sepet öğesi
     * @param User|null $user Kullanıcı (opsiyonel)
     * @return PriceResult Fiyatlandırma sonucu
     */
    public function calculateItemPrice(CartItem $item, ?User $user = null): PriceResult
    {
        return $this->pricingService->calculatePrice(
            $item->productVariant,
            $item->quantity,
            $user ?? $item->cart->user
        );
    }

    /**
     * Sepetteki tüm kalemlerin fiyatlarını günceller.
     *
     * @param Cart $cart Sepet
     * @return void
     */
    private function updateAllItemPrices(Cart $cart): void
    {
        $cart->load(['items.productVariant', 'user']);

        foreach ($cart->items as $item) {
            $this->updateItemPrice($item, $cart->user);
        }
    }

    /**
     * Tek bir sepet öğesinin fiyatını günceller.
     *
     * @param CartItem $item Sepet öğesi
     * @param User|null $user Kullanıcı
     * @return void
     */
    private function updateItemPrice(CartItem $item, ?User $user): void
    {
        try {
            $priceResult = $this->pricingService->calculatePrice(
                $item->productVariant,
                $item->quantity,
                $user
            );

            $appliedDiscounts = [];
            if ($priceResult->getDiscount()) {
                $appliedDiscounts[] = [
                    'type' => 'pricing_service',
                    'amount' => $priceResult->getDiscount()->getAmount(),
                    'description' => $priceResult->getDiscount()->getDescription() ?? 'Automatic discount'
                ];
            }

            $item->update([
                'base_price' => $priceResult->getBasePrice()->getAmount(),
                'calculated_price' => $priceResult->getFinalPrice()->getAmount(),
                'price' => $priceResult->getFinalPrice()->getAmount(), // Geriye dönük uyumluluk için
                'discounted_price' => $priceResult->getFinalPrice()->getAmount(),
                'unit_discount' => $priceResult->getDiscount()?->getAmount() ?? 0,
                'total_discount' => ($priceResult->getDiscount()?->getAmount() ?? 0) * $item->quantity,
                'applied_discounts' => $appliedDiscounts,
                'price_calculated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update item price', [
                'item_id' => $item->id,
                'cart_id' => $item->cart_id,
                'error' => $e->getMessage()
            ]);
            
            // Hesaplama başarısız olursa mevcut fiyatı koru
            if (!$item->price_calculated_at) {
                $item->update([
                    'calculated_price' => $item->productVariant->price,
                    'price' => $item->productVariant->price,
                    'price_calculated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Sepet özetini hesaplamanın gerçek işini yapar.
     *
     * @param Cart $cart Sepet
     * @return CartSummary Sepet özeti
     */
    private function performCartSummaryCalculation(Cart $cart): CartSummary
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $itemDetails = [];
        
        // Kalemlerden toplamları hesapla
        foreach ($cart->items as $item) {
            $itemSubtotal = ($item->calculated_price ?? $item->price ?? 0) * $item->quantity;
            $itemDiscount = $item->total_discount ?? 0;
            
            $subtotal += $itemSubtotal;
            $totalDiscount += $itemDiscount;
            
            $itemDetails[] = [
                'item_id' => $item->id,
                'product_name' => $item->product->name,
                'variant_name' => $item->productVariant->name ?? '',
                'quantity' => $item->quantity,
                'base_price' => $item->base_price ?? $item->productVariant->price,
                'calculated_price' => $item->calculated_price ?? $item->price,
                'unit_discount' => $item->unit_discount ?? 0,
                'total_discount' => $item->total_discount ?? 0,
                'subtotal' => $itemSubtotal,
                'applied_discounts' => $item->applied_discounts ?? []
            ];
        }

        // Sepet seviyesindeki indirimleri uygula (kupon vb.)
        $cartLevelDiscount = $cart->coupon_discount ?? 0;
        $totalDiscount += $cartLevelDiscount;

        // Uygulanan tüm indirimleri topla
        $appliedDiscounts = [];
        
        // Kalem seviyesindeki indirimleri ekle
        foreach ($cart->items as $item) {
            if ($item->applied_discounts) {
                $appliedDiscounts = array_merge($appliedDiscounts, $item->applied_discounts);
            }
        }
        
        // Sepet seviyesindeki indirimleri ekle
        if ($cartLevelDiscount > 0) {
            $appliedDiscounts[] = [
                'type' => 'coupon',
                'code' => $cart->coupon_code,
                'amount' => $cartLevelDiscount,
                'description' => "Coupon: {$cart->coupon_code}"
            ];
        }

        $finalTotal = max(0, $subtotal - $totalDiscount);

        return new CartSummary(
            subtotal: $subtotal,
            discount: $totalDiscount,
            total: $finalTotal,
            itemCount: $cart->items->sum('quantity'),
            itemDetails: $itemDetails,
            appliedDiscounts: $appliedDiscounts
        );
    }
}