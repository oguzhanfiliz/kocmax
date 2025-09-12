<?php

declare(strict_types=1);

namespace App\Services\Campaign\Handlers;

use App\Contracts\Campaign\CampaignHandlerInterface;
use App\Enums\Campaign\CampaignType;
use App\Models\Campaign;
use App\ValueObjects\Campaign\CampaignResult;
use App\ValueObjects\Campaign\CartContext;
use App\ValueObjects\Pricing\Discount;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Flash Sale (ani satış) kampanyası için handler.
 *
 * Belirlenen ürün(ler) ve kurallara göre sepet toplamına ani indirim uygular.
 */
class FlashSaleHandler implements CampaignHandlerInterface
{
    /**
     * Bu handler'ın desteklediği kampanya türünü doğrular.
     *
     * @param Campaign $campaign Kampanya
     * @return bool Destekliyorsa true
     */
    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::FLASH_SALE->value;
    }

    /**
     * Kampanyanın belirtilen sepet bağlamında uygulanabilirliğini kontrol eder.
     *
     * @param Campaign $campaign Kampanya
     * @param CartContext $context Sepet bağlamı
     * @param User|null $user Kullanıcı (opsiyonel)
     * @return bool Uygulanabilirse true
     */
    public function canApply(Campaign $campaign, CartContext $context, ?User $user = null): bool
    {
        return $this->validateCampaign($campaign) && $this->validateContext($context);
    }

    /**
     * Bu handler'ın desteklediği kampanya türü anahtarını döndürür.
     *
     * @return string Kampanya türü anahtarı
     */
    public function getSupportedType(): string
    {
        return CampaignType::FLASH_SALE->value;
    }

    /**
     * Handler önceliği (yüksek sayı = yüksek öncelik).
     *
     * @return int Öncelik
     */
    public function getPriority(): int
    {
        return 100; // Yüksek öncelik (flash sale)
    }

    /**
     * Kampanyayı uygular ve sonucu döndürür.
     *
     * @param Campaign $campaign Kampanya
     * @param CartContext $context Sepet bağlamı
     * @param User|null $user Kullanıcı (opsiyonel)
     * @return CampaignResult Kampanya sonucu
     */
    public function apply(Campaign $campaign, CartContext $context, ?User $user = null): CampaignResult
    {
        try {
            // Doğrulama zinciri
            if (!$this->validateCampaign($campaign)) {
                return CampaignResult::failed('Kampanya doğrulaması başarısız');
            }

            if (!$this->validateContext($context)) {
                return CampaignResult::failed('Geçersiz sepet bağlamı');
            }

            // Flash sale indirimini hesapla
            $discountAmount = $this->calculateFlashDiscount($campaign, $context);
            
            if ($discountAmount <= 0) {
                return CampaignResult::failed('Uygulanabilir indirim yok');
            }

            Log::info('Flash sale applied', [
                'campaign_id' => $campaign->id,
                'customer_id' => $context->getCustomerId(),
                'discount_amount' => $discountAmount,
                'cart_total' => $context->getTotalAmount()
            ]);

            return CampaignResult::discount(
                new Discount($discountAmount, 'Flash Sale: ' . $campaign->name),
                "Flaş indirim uygulandı: {$campaign->name}"
            );

        } catch (\Exception $e) {
            Log::error('Flash sale handler failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return CampaignResult::failed('Flash sale hesaplama başarısız');
        }
    }

    /**
     * Kampanyanın aktiflik, tarih ve kural geçerliliğini doğrular.
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

        // Geçerli flash sale kuralları olmalı
        $rules = $campaign->rules ?? [];
        if (empty($rules['flash_discount_type']) || empty($rules['flash_discount_value'])) {
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
     * Flash sale indirim tutarını hesaplar.
     *
     * @param Campaign $campaign Kampanya
     * @param CartContext $context Sepet bağlamı
     * @return float İndirim tutarı
     */
    private function calculateFlashDiscount(Campaign $campaign, CartContext $context): float
    {
        $rules = $campaign->rules ?? [];
        $rewards = $campaign->rewards ?? [];
        
        $discountType = $rules['flash_discount_type'] ?? 'percentage';
        $discountValue = $rules['flash_discount_value'] ?? 0;
        $targetProducts = $rules['flash_sale_products'] ?? []; // Belirli ürünler veya tümü için boş

        $cartItems = $context->getItems();
        $applicableAmount = 0;

        // Uygun ürünlerin toplam tutarını hesapla
        foreach ($cartItems as $item) {
            $isApplicable = empty($targetProducts) || in_array($item['product_id'], $targetProducts);
            
            if ($isApplicable) {
                $applicableAmount += $item['price'] * $item['quantity'];
            }
        }

        if ($applicableAmount <= 0) {
            return 0;
        }

        // İndirim tutarını hesapla
        $discountAmount = match ($discountType) {
            'percentage' => ($applicableAmount * $discountValue) / 100,
            'fixed' => min($discountValue, $applicableAmount), // Uygulanabilir tutarı aşma
            default => 0
        };

        // Maksimum indirim sınırını uygula
        $maxDiscount = $rewards['max_discount'] ?? null;
        if ($maxDiscount && $discountAmount > $maxDiscount) {
            $discountAmount = $maxDiscount;
        }

        // Sepet toplamı ile sınırla
        $cartTotal = $context->getTotalAmount();
        return min($discountAmount, $cartTotal);
    }
}