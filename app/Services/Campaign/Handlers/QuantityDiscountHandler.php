<?php

declare(strict_types=1);

namespace App\Services\Campaign\Handlers;

use App\Contracts\Campaign\CampaignHandlerInterface;
use App\Enums\Campaign\CampaignType;
use App\Models\Campaign;
use App\ValueObjects\Campaign\CampaignResult;
use App\ValueObjects\Campaign\CartContext;
use App\ValueObjects\Pricing\Discount;
use Illuminate\Support\Facades\Log;

/**
 * Miktar indirimi kampanyası için handler.
 *
 * Uygun ürün(ler) ve miktar kademelerine göre indirim hesaplar.
 */
class QuantityDiscountHandler implements CampaignHandlerInterface
{
    /**
     * Bu handler'ın desteklediği kampanya türünü doğrular.
     *
     * @param Campaign $campaign Kampanya
     * @return bool Destekliyorsa true
     */
    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::QUANTITY_DISCOUNT->value;
    }

    /**
     * Kampanyayı uygular ve sonucu döndürür.
     *
     * @param Campaign $campaign Kampanya
     * @param CartContext $context Sepet bağlamı
     * @return CampaignResult Kampanya sonucu
     */
    public function apply(Campaign $campaign, CartContext $context): CampaignResult
    {
        try {
            // Doğrulama zinciri
            if (!$this->validateCampaign($campaign)) {
                return CampaignResult::failed('Kampanya doğrulaması başarısız');
            }

            if (!$this->validateContext($context)) {
                return CampaignResult::failed('Geçersiz sepet bağlamı');
            }

            // Miktar indirimini hesapla
            $discountResult = $this->calculateQuantityDiscount($campaign, $context);
            
            if ($discountResult['discount_amount'] <= 0) {
                return CampaignResult::failed($discountResult['reason'] ?? 'İndirim uygulanamadı');
            }

            Log::info('Miktar indirimi uygulandı', [
                'campaign_id' => $campaign->id,
                'customer_id' => $context->getCustomerId(),
                'qualifying_products' => $discountResult['qualifying_products'],
                'total_quantity' => $discountResult['total_quantity'],
                'discount_amount' => $discountResult['discount_amount'],
                'tier_applied' => $discountResult['tier_applied']
            ]);

            return CampaignResult::discount(
                new Discount($discountResult['discount_amount'], 'Miktar İndirimi: ' . $campaign->name),
                "Miktar indirimi uygulandı: {$campaign->name} ({$discountResult['tier_applied']['description']})"
            );

        } catch (\Exception $e) {
            Log::error('Miktar indirimi handler hatası', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return CampaignResult::failed('Miktar indirimi hesaplaması başarısız');
        }
    }

    /**
     * Kampanya temel doğrulamaları (aktiflik, tarih ve kural kademeleri).
     *
     * @param Campaign $campaign Kampanya
     * @return bool Geçerliyse true
     */
    private function validateCampaign(Campaign $campaign): bool
    {
        // Kampanya aktif olmalı
        if (!$campaign->is_active) {
            return false;
        }

        // Kampanya tarih aralığı içinde olmalı
        $now = now();
        if ($campaign->starts_at && $now->lt($campaign->starts_at)) {
            return false;
        }

        if ($campaign->ends_at && $now->gt($campaign->ends_at)) {
            return false;
        }

        // Geçerli miktar kademeleri olmalı
        $rules = $campaign->rules ?? [];
        if (empty($rules['quantity_tiers']) || !is_array($rules['quantity_tiers'])) {
            return false;
        }

        return true;
    }

    /**
     * Sepet bağlamının geçerliliğini kontrol eder.
     *
     * @param CartContext $context Sepet bağlamı
     * @return bool Geçerliyse true
     */
    private function validateContext(CartContext $context): bool
    {
        return $context->getItems()->isNotEmpty() && $context->getTotalAmount() > 0;
    }

    /**
     * Miktar indirimi hesaplamasını yapar ve detaylı sonuç döndürür.
     *
     * @param Campaign $campaign Kampanya
     * @param CartContext $context Sepet bağlamı
     * @return array{discount_amount:float,qualifying_products:array,total_quantity:int,tier_applied:array,apply_to_all:bool,reason?:string}
     */
    private function calculateQuantityDiscount(Campaign $campaign, CartContext $context): array
    {
        $rules = $campaign->rules ?? [];
        $rewards = $campaign->rewards ?? [];
        
        $quantityTiers = $rules['quantity_tiers'] ?? [];
        $targetProducts = $rules['target_products'] ?? []; // Belirli ürünler veya tümü için boş
        $targetCategories = $rules['target_categories'] ?? [];
        $applyToAll = $rules['apply_to_all'] ?? true; // Tüm ürünler mi yoksa sadece uygun olanlar mı
        
        // Uygun ürünleri al ve toplam adedi hesapla
        $qualifyingResult = $this->getQualifyingProducts($context, $targetProducts, $targetCategories);
        
        if ($qualifyingResult['total_quantity'] <= 0) {
            return [
                'discount_amount' => 0,
                'reason' => 'Miktar indirimi için uygun ürün bulunamadı'
            ];
        }

        // Uygulanabilir kademeyi bul
        $applicableTier = $this->findApplicableTier($quantityTiers, $qualifyingResult['total_quantity']);
        
        if (!$applicableTier) {
            return [
                'discount_amount' => 0,
                'reason' => 'Minimum miktar şartı karşılanmadı'
            ];
        }

        // Kademeye göre indirim tutarını hesapla
        $discountAmount = $this->calculateTierDiscount(
            $applicableTier, 
            $qualifyingResult, 
            $context, 
            $applyToAll
        );

        // Maksimum indirim sınırını uygula
        $maxDiscount = $rewards['max_discount'] ?? null;
        if ($maxDiscount && $discountAmount > $maxDiscount) {
            $discountAmount = $maxDiscount;
        }

        // Sepet toplamı ile sınırla
        $cartTotal = $context->getTotalAmount();
        $discountAmount = min($discountAmount, $cartTotal);

        return [
            'discount_amount' => $discountAmount,
            'qualifying_products' => $qualifyingResult['products'],
            'total_quantity' => $qualifyingResult['total_quantity'],
            'tier_applied' => $applicableTier,
            'apply_to_all' => $applyToAll
        ];
    }

    /**
     * Hedef ürün/kategorilere göre uygun ürünleri ve toplam miktarı döndürür.
     *
     * @param CartContext $context Sepet bağlamı
     * @param array $targetProducts Hedef ürünler
     * @param array $targetCategories Hedef kategoriler
     * @return array{products:array,total_quantity:int}
     */
    private function getQualifyingProducts(CartContext $context, array $targetProducts, array $targetCategories): array
    {
        $cartItems = $context->getItems();
        $qualifyingProducts = [];
        $totalQuantity = 0;

        foreach ($cartItems as $item) {
            $isQualifying = false;

            // Belirli ürünler hedeflenmiş mi kontrol et
            if (!empty($targetProducts)) {
                $isQualifying = in_array($item['product_id'], $targetProducts);
            }
            // Belirli kategoriler hedeflenmiş mi kontrol et
            elseif (!empty($targetCategories)) {
                $productCategories = $item['categories'] ?? [];
                $isQualifying = !empty(array_intersect($productCategories, $targetCategories));
            }
            // Spesifik hedef yoksa tüm ürünler uygundur
            else {
                $isQualifying = true;
            }

            if ($isQualifying) {
                $qualifyingProducts[] = $item;
                $totalQuantity += $item['quantity'];
            }
        }

        return [
            'products' => $qualifyingProducts,
            'total_quantity' => $totalQuantity
        ];
    }

    /**
     * Toplam miktara göre uygulanabilir kademeyi bulur.
     *
     * @param array $tiers Kademe tanımları
     * @param int $totalQuantity Toplam miktar
     * @return array|null Uygun kademe veya null
     */
    private function findApplicableTier(array $tiers, int $totalQuantity): ?array
    {
        // En yüksek uygulanabilir kademeyi bulmak için minimum miktara göre azalan sırala
        usort($tiers, function ($a, $b) {
            return ($b['min_quantity'] ?? 0) <=> ($a['min_quantity'] ?? 0);
        });

        foreach ($tiers as $tier) {
            $minQuantity = $tier['min_quantity'] ?? 0;
            if ($totalQuantity >= $minQuantity) {
                return $tier;
            }
        }

        return null;
    }

    /**
     * Kademeye göre indirim tutarını hesaplar.
     *
     * @param array $tier Uygulanan kademe
     * @param array $qualifyingResult Uygun ürünler ve toplam miktar
     * @param CartContext $context Sepet bağlamı
     * @param bool $applyToAll Tüm ürünlere mi uygulanacak?
     * @return float İndirim tutarı
     */
    private function calculateTierDiscount(array $tier, array $qualifyingResult, CartContext $context, bool $applyToAll): float
    {
        $discountType = $tier['discount_type'] ?? 'percentage';
        $discountValue = $tier['discount_value'] ?? 0;
        $maxQuantity = $tier['max_quantity'] ?? null; // İndirimin uygulanacağı maksimum miktar
        
        // İndirimin uygulanacağı ürünleri belirle
        $targetProducts = $applyToAll ? $context->getItems()->toArray() : $qualifyingResult['products'];
        
        $totalDiscount = 0;
        $processedQuantity = 0;

        foreach ($targetProducts as $product) {
            if ($maxQuantity && $processedQuantity >= $maxQuantity) {
                break;
            }

            $productQuantity = $product['quantity'];
            $productPrice = $product['price'];
            
            // Miktar limiti varsa uygula
            if ($maxQuantity) {
                $remainingLimit = $maxQuantity - $processedQuantity;
                $applicableQuantity = min($productQuantity, $remainingLimit);
            } else {
                $applicableQuantity = $productQuantity;
            }

            $productDiscount = match ($discountType) {
                'percentage' => ($productPrice * $applicableQuantity * $discountValue) / 100,
                'fixed' => $discountValue, // Fixed discount total (not per item)
                'fixed_per_item' => $discountValue * $applicableQuantity,
                'tiered_percentage' => $this->calculateTieredPercentageDiscount($tier, $applicableQuantity, $productPrice),
                default => 0
            };

            $totalDiscount += $productDiscount;
            $processedQuantity += $applicableQuantity;
        }

        return $totalDiscount;
    }

    /**
     * Kademeli yüzde indirim hesaplamasını yapar.
     *
     * @param array $tier Kademe
     * @param int $quantity Adet
     * @param float $unitPrice Birim fiyat
     * @return float İndirim tutarı
     */
    private function calculateTieredPercentageDiscount(array $tier, int $quantity, float $unitPrice): float
    {
        $tieredRates = $tier['tiered_rates'] ?? [];
        if (empty($tieredRates)) {
            return 0;
        }

        // Kademe oranlarını eşik miktarına göre sırala
        usort($tieredRates, function ($a, $b) {
            return ($a['from_quantity'] ?? 0) <=> ($b['from_quantity'] ?? 0);
        });

        $totalDiscount = 0;
        $remainingQuantity = $quantity;

        foreach ($tieredRates as $rate) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $fromQuantity = $rate['from_quantity'] ?? 0;
            $toQuantity = $rate['to_quantity'] ?? PHP_INT_MAX;
            $discountPercentage = $rate['discount_percentage'] ?? 0;

            // Bu kademeye henüz ulaşılmadıysa geç
            if ($quantity < $fromQuantity) {
                continue;
            }

            // Bu kademe içindeki miktarı hesapla
            $tierStartQuantity = max(0, $fromQuantity - ($quantity - $remainingQuantity));
            $tierEndQuantity = min($remainingQuantity, $toQuantity - ($quantity - $remainingQuantity));
            $tierQuantity = max(0, $tierEndQuantity - $tierStartQuantity);

            if ($tierQuantity > 0) {
                $tierDiscount = ($unitPrice * $tierQuantity * $discountPercentage) / 100;
                $totalDiscount += $tierDiscount;
                $remainingQuantity -= $tierQuantity;
            }
        }

        return $totalDiscount;
    }
}