<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Müşteri tipini tespit eden servis - B2B/B2C/WHOLESALE/RETAIL/Guest ayırımı.
 *
 * Request ve User bilgilerinden müşteri tipini belirler, müşteri katmanını (tier)
 * ve strateji etiketini üretir, ayrıca akıllı fiyatlandırma için indirim yüzdesi
 * hesaplamasını sağlar.
 */
class CustomerTypeDetectorService
{
    /**
     * HTTP isteğinden müşteri tipini ve ilgili bilgileri tespit eder.
     *
     * @param Request $request HTTP isteği
     * @return array{type:string,user:?\App\Models\User,is_authenticated:bool,is_dealer:bool,pricing_tier_id: mixed}
     */
    public function detectFromRequest(Request $request): array
    {
        $user = $request->user();
        
        return [
            'type' => $this->getCustomerType($user),
            'user' => $user,
            'is_authenticated' => $user !== null,
            'is_dealer' => $this->isDealer($user),
            'pricing_tier_id' => $user?->pricing_tier_id,
        ];
    }
    
    /**
     * User nesnesinden müşteri tipini belirler.
     *
     * @param User|null $user Kullanıcı (opsiyonel)
     * @return string Müşteri tipi (B2B, B2C, WHOLESALE, RETAIL, GUEST)
     */
    public function getCustomerType(?User $user): string
    {
        if (!$user) {
            return 'B2C';
        }
        
        // Müşteri tipi override varsa öncelik ver
        if (!empty($user->customer_type_override)) {
            return strtoupper($user->customer_type_override);
        }
        
        // Roller kontrol et
        // Admin / Manager kullanıcıları için fiyat önizlemelerinde B2B davranışı istenir
        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            return 'B2B';
        }

        if ($user->hasRole('wholesale')) {
            return 'WHOLESALE';
        }
        
        if ($user->hasRole('dealer') || $user->is_approved_dealer) {
            return 'B2B';
        }
        
        if ($user->hasRole('retail')) {
            return 'RETAIL';
        }
        
        // Şirket bilgisi varsa B2B kabul et
        if (!empty($user->company_name) || !empty($user->tax_number)) {
            return 'B2B';
        }
        
        // Lifetime value yüksekse wholesale
        if (($user->lifetime_value ?? 0) >= 50000) {
            return 'WHOLESALE';
        }
        
        return 'B2C';
    }
    
    /**
     * Kullanıcının dealer (bayi) olup olmadığını kontrol eder.
     *
     * @param User|null $user Kullanıcı (opsiyonel)
     * @return bool Dealer ise true
     */
    public function isDealer(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        
        $customerType = $this->getCustomerType($user);
        return in_array($customerType, ['B2B', 'WHOLESALE']);
    }
    
    /**
     * Kullanıcının müşteri katmanını (tier) belirler.
     *
     * @param User|null $user Kullanıcı (opsiyonel)
     * @return string Tier anahtarı
     */
    public function getCustomerTier(?User $user): string
    {
        if (!$user) {
            return 'guest';
        }

        $customerType = $this->getCustomerType($user);
        
        // Sipariş geçmişi ve tipe göre müşteri katmanını belirle
        $totalOrders = $user->orders()->completed()->count();
        $totalSpent = (float) ($user->orders()->completed()->sum('total_amount') ?? 0);

        if (in_array($customerType, ['B2B', 'WHOLESALE'])) {
            return match(true) {
                $totalSpent >= 500000 => 'b2b_vip',
                $totalSpent >= 250000 => 'b2b_premium',
                $totalSpent >= 100000 => 'b2b_gold',
                $totalSpent >= 50000 => 'b2b_silver',
                default => 'b2b_standard'
            };
        }

        return match(true) {
            $totalOrders >= 50 => 'b2c_vip',
            $totalOrders >= 25 => 'b2c_gold',
            $totalOrders >= 10 => 'b2c_silver',
            $totalOrders >= 5 => 'b2c_bronze',
            default => 'b2c_standard'
        };
    }
    
    /**
     * Müşteri tipine göre fiyatlandırma stratejisi anahtarı döndürür.
     *
     * @param string $customerType Müşteri tipi
     * @return string Strateji anahtarı (dealer|retail|guest)
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
     * Müşteri tipine ve miktara göre indirim yüzdesini hesaplar.
     *
     * @param User|null $user Kullanıcı (opsiyonel)
     * @param int $quantity Adet
     * @return float İndirim yüzdesi
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
     * Uygulanabilir pricing rule'ı bulur (özel: veritabanı sorgusu).
     *
     * @param string $customerType Müşteri tipi
     * @param int $quantity Adet
     * @return \App\Models\PricingRule|null Uygun kural veya null
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
     * Müşteri tipi için görüntülenecek etiketi döndürür.
     *
     * @param string $customerType Müşteri tipi
     * @return string Etiket
     */
    public function getTypeLabel(string $customerType): string
    {
        return match ($customerType) {
            'B2B' => '🏢 Bayi Fiyatı',
            'B2C' => '👤 Bireysel Fiyat',
            'WHOLESALE' => '📦 Toptan Fiyat',
            'RETAIL' => '🛍️ Perakende Fiyat',
            'GUEST' => 'Liste Fiyatı',
            default => 'Standart Fiyat'
        };
    }
    
    /**
     * Özellik bayrağı kontrolü - Akıllı fiyatlandırma aktif mi?
     *
     * @return bool Aktifse true
     */
    public function isSmartPricingEnabled(): bool
    {
        return config('features.smart_pricing_enabled', true);
    }
    
    /**
     * Önbellek anahtarı üretir (kullanıcı segmentine göre).
     *
     * @param string $baseKey Temel anahtar
     * @param string $customerType Müşteri tipi
     * @param int|null $userId Kullanıcı ID (opsiyonel)
     * @return string Önbellek anahtarı
     */
    public function getCacheKey(string $baseKey, string $customerType, ?int $userId = null): string
    {
        $userSegment = $userId ? "user_{$userId}" : $customerType;
        return "{$baseKey}.{$userSegment}";
    }
}
