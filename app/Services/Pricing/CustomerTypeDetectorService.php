<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Kullanıcı tipini tespit eden servis - B2B/B2C/Guest ayırımı
 */
class CustomerTypeDetectorService
{
    /**
     * Request'ten user tipini belirle
     */
    public function detectFromRequest(Request $request): array
    {
        $user = $request->user();
        
        return [
            'type' => $this->getCustomerType($user),
            'user' => $user,
            'is_authenticated' => $user !== null,
            'is_dealer' => $user && $user->is_approved_dealer,
            'pricing_tier_id' => $user?->pricing_tier_id,
        ];
    }
    
    /**
     * User objesinden customer type belirle
     */
    public function getCustomerType(?User $user): string
    {
        if (!$user) {
            return 'B2C';
        }
        
        if ($user->is_approved_dealer) {
            return 'B2B';
        }
        
        return 'B2C';
    }
    
    /**
     * Customer type'a göre fiyatlandırma stratejisi belirle
     */
    public function getPricingStrategy(string $customerType): string
    {
        return match ($customerType) {
            'B2B' => 'dealer',
            'B2C' => 'retail',
            'guest' => 'guest',
            default => 'guest'
        };
    }
    
    /**
     * Müşteri tipine göre indirim yüzdesi hesapla
     */
    public function getDiscountPercentage(?User $user, int $quantity = 1): float
    {
        $customerType = $this->getCustomerType($user);
        
        // PricingRule'lardan indirim al
        $pricingRule = $this->getApplicablePricingRule($customerType, $quantity);
        if ($pricingRule) {
            return $pricingRule->actions['discount_percentage'] ?? 0.0;
        }
        
        // Eğer user varsa pricing tier'dan indirim al (fallback)
        if ($user && $user->pricingTier && $user->pricingTier->isActive()) {
            return $user->pricingTier->discount_percentage ?? 0.0;
        }
        
        // Eğer user varsa custom discount varsa
        if ($user && $user->custom_discount_percentage > 0) {
            return $user->custom_discount_percentage;
        }
        
        // Hiçbir indirim yok
        return 0.0;
    }

    /**
     * Uygulanabilir pricing rule'ı bul
     */
    private function getApplicablePricingRule(string $customerType, int $quantity): ?\App\Models\PricingRule
    {
        return \App\Models\PricingRule::where('is_active', true)
            ->where('conditions->customer_types', 'like', '%"' . strtolower($customerType) . '"%')
            ->where('conditions->min_quantity', '<=', $quantity)
            ->orderBy('priority', 'desc')
            ->first();
    }
    
    /**
     * Customer type için display label döndür
     */
    public function getTypeLabel(string $customerType): string
    {
        return match ($customerType) {
            'B2B' => '🏢 Bayi Fiyatı',
            'B2C' => '👤 Bireysel Fiyat',
            'WHOLESALE' => '📦 Toptan Fiyat',
            'RETAIL' => '🛍️ Perakende Fiyat',
            default => 'Standart Fiyat'
        };
    }
    
    /**
     * Feature flag kontrolü - Smart pricing aktif mi?
     */
    public function isSmartPricingEnabled(): bool
    {
        return config('features.smart_pricing_enabled', true);
    }
    
    /**
     * Cache key oluştur (user tipine göre)
     */
    public function getCacheKey(string $baseKey, string $customerType, ?int $userId = null): string
    {
        $userSegment = $userId ? "user_{$userId}" : $customerType;
        return "{$baseKey}.{$userSegment}";
    }
}
