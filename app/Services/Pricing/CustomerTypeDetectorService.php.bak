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
            return 'guest';
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
    public function getDiscountPercentage(?User $user): float
    {
        if (!$user || !$user->is_approved_dealer) {
            return 0.0;
        }
        
        // Pricing tier'dan indirim al
        if ($user->pricingTier) {
            return $user->pricingTier->discount_percentage ?? 0.0;
        }
        
        // Default dealer discount (fallback)
        return config('pricing.default_dealer_discount', 15.0);
    }
    
    /**
     * Customer type için display label döndür
     */
    public function getTypeLabel(string $customerType): string
    {
        return match ($customerType) {
            'B2B' => 'Bayi Fiyatı',
            'B2C' => 'Perakende Fiyatı',
            'guest' => 'Liste Fiyatı',
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
