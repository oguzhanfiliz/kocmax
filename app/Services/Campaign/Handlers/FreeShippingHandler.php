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
 * Ücretsiz Kargo kampanyası için kampanya işleyicisi (handler).
 *
 * Sepet bağlamına göre ücretsiz kargonun uygulanabilirliğini kontrol eder,
 * uygunsa kargo maliyeti kadar indirim döndürür.
 */
class FreeShippingHandler implements CampaignHandlerInterface
{
    /**
     * Kampanya türü bu handler tarafından destekleniyor mu?
     *
     * @param Campaign $campaign Kampanya modeli
     * @return bool Destekliyorsa true
     */
    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::FREE_SHIPPING->value;
    }

    /**
     * Kampanyanın belirtilen sepet bağlamında uygulanabilir olup olmadığını kontrol eder.
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
     * Bu handler'ın desteklediği kampanya türünü döndürür.
     *
     * @return string Kampanya türü anahtarı
     */
    public function getSupportedType(): string
    {
        return CampaignType::FREE_SHIPPING->value;
    }

    /**
     * Bu handler'ın öncelik seviyesini döndürür (yüksek sayı = yüksek öncelik).
     *
     * @return int Öncelik
     */
    public function getPriority(): int
    {
        return 30; // Düşük öncelik (shipping)
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

            // Ücretsiz kargonun uygulanabilir olup olmadığını kontrol et
            $shippingBenefit = $this->calculateShippingBenefit($campaign, $context);
            
            if (!$shippingBenefit['applicable']) {
                return CampaignResult::failed($shippingBenefit['reason']);
            }

            Log::info('Ücretsiz kargo uygulandı', [
                'campaign_id' => $campaign->id,
                'customer_id' => $context->getCustomerId(),
                'shipping_cost_saved' => $shippingBenefit['shipping_cost'],
                'trigger_reason' => $shippingBenefit['trigger_reason']
            ]);

            // İndirim olarak döndür (kargo maliyeti tasarrufu)
            return CampaignResult::discount(
                new Discount($shippingBenefit['shipping_cost'], 'Ücretsiz Kargo: ' . $campaign->name),
                "Ücretsiz kargo uygulandı: {$campaign->name}"
            );

        } catch (\Exception $e) {
            Log::error('Ücretsiz kargo işleyicisi başarısız', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return CampaignResult::failed('Ücretsiz kargo hesaplama başarısız');
        }
    }

    /**
     * Kampanyanın aktiflik ve tarih aralığı kriterlerini doğrular.
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
     * Ücretsiz kargonun uygulanabilirliğini ve faydasını hesaplar.
     *
     * @param Campaign $campaign Kampanya
     * @param CartContext $context Sepet bağlamı
     * @return array{applicable:bool,shipping_cost?:float,trigger_reason?:string,reason?:string}
     */
    private function calculateShippingBenefit(Campaign $campaign, CartContext $context): array
    {
        // Kampanya tipine özgü alanları kullan
        $minAmount = $campaign->free_shipping_min_amount ?? 0;
        $specialProducts = $campaign->products->pluck('id')->toArray();
        $standardShippingCost = config('shipping.default_cost', 25); // Yapılandırılabilir kargo maliyeti

        $cartTotal = $context->getTotalAmount();
        $cartItems = $context->getItems();

        // Minimum tutar şartı sağlanıyor mu kontrol et
        if ($minAmount > 0 && $cartTotal >= $minAmount) {
            return [
                'applicable' => true,
                'shipping_cost' => $standardShippingCost,
                'trigger_reason' => "Minimum tutar {$minAmount}₺'ye ulaşıldı"
            ];
        }

        // Sepette ücretsiz kargoya uygun özel ürünler var mı kontrol et
        if (!empty($specialProducts)) {
            foreach ($cartItems as $item) {
                if (in_array($item['product_id'], $specialProducts)) {
                    return [
                        'applicable' => true,
                        'shipping_cost' => $standardShippingCost,
                        'trigger_reason' => 'Sepette ücretsiz kargoya uygun ürünler var'
                    ];
                }
            }
        }

        // Tüm ürünler ücretsiz kargo kapsamındaysa
        if (empty($specialProducts) && $minAmount <= 0) {
            return [
                'applicable' => true,
                'shipping_cost' => $standardShippingCost,
                'trigger_reason' => 'Evrensel ücretsiz kargo kampanyası'
            ];
        }

        return [
            'applicable' => false,
            'reason' => $minAmount > 0 
                ? "Minimum sepet tutarı {$minAmount}₺'ye ulaşılamadı (mevcut: {$cartTotal}₺)"
                : 'Ücretsiz kargoya uygun ürünler yok'
        ];
    }
}