<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Enums\Pricing\CustomerType;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Müşteri tipini (B2B, B2C, WHOLESALE, RETAIL, GUEST) belirleyen yardımcı sınıf.
 *
 * Kullanıcı rolü, bağlam bilgisi ve kullanıcı özelliklerine göre müşteri tipini
 * tespit eder; ayrıca yardımcı kontrol metodları sağlar.
 */
class CustomerTypeDetector
{
    /**
     * Kullanıcı ve bağlam bilgisine göre müşteri tipini tespit eder.
     *
     * @param User|null $customer Kullanıcı (opsiyonel)
     * @param array $context Ek bağlam (force_type, order_quantity vb.)
     * @return CustomerType Tespit edilen müşteri tipi
     */
    public function detect(?User $customer = null, array $context = []): CustomerType
    {
        if (!$customer) {
            return CustomerType::GUEST;
        }

        // Bağlam yoksa önbellekten almaya çalış
        if (empty($context)) {
            $cacheKey = "customer_type_{$customer->id}";
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return CustomerType::from($cached);
            }
        }

        $customerType = $this->doDetect($customer, $context);

        // Bağlam sağlanmadıysa sonucu önbelleğe al
        if (empty($context)) {
            Cache::put("customer_type_{$customer->id}", $customerType->value, 3600); // 1 saat
        }

        return $customerType;
    }

    /**
     * Asıl tespit mantığını uygular (rol, bağlam ve kullanıcı alanları).
     *
     * @param User $customer Kullanıcı
     * @param array $context Bağlam
     * @return CustomerType Müşteri tipi
     */
    private function doDetect(User $customer, array $context = []): CustomerType
    {
        // Önce müşteri tipi override var mı kontrol et
        if (!empty($customer->customer_type_override)) {
            return CustomerType::from($customer->customer_type_override);
        }

        // Bağlamda zorunlu tip (force_type) var mı kontrol et
        if (isset($context['force_type'])) {
            return CustomerType::from($context['force_type']);
        }

        // Bağlamda B2B davranış göstergelerini kontrol et
        if (isset($context['order_quantity']) && $context['order_quantity'] >= 100) {
            return CustomerType::B2B;
        }

        if (isset($context['order_frequency']) && $context['order_frequency'] === 'high') {
            return CustomerType::B2B;
        }

        // Yüksek hacimli toptan uygunluk kriterini kontrol et
        if (($customer->lifetime_value ?? 0) >= 50000) {
            return CustomerType::WHOLESALE;
        }

        // Müşteri tipini belirlemek için kullanıcı rollerini kontrol et
        if ($customer->hasRole('dealer')) {
            return CustomerType::B2B;
        }

        if ($customer->hasRole('wholesale')) {
            return CustomerType::WHOLESALE;
        }

        if ($customer->hasRole('retail')) {
            return CustomerType::RETAIL;
        }

        // Kullanıcının bayiyle ilgili alanları var mı kontrol et
        if ($customer->is_approved_dealer ?? false) {
            return CustomerType::B2B;
        }

        // Firma bilgilerini kontrol et
        if (!empty($customer->company_name) || !empty($customer->tax_number)) {
            return CustomerType::B2B;
        }

        // Özel iş göstergeleri olmayan kayıtlı kullanıcılar için varsayılan: B2C
        return CustomerType::B2C;
    }

    /**
     * Kullanıcının B2B müşteri olup olmadığını döndürür.
     *
     * @param User|null $customer Kullanıcı (opsiyonel)
     * @return bool B2B ise true
     */
    public function isB2BCustomer(?User $customer = null): bool
    {
        return $this->detect($customer)->isB2B();
    }

    /**
     * Kullanıcının B2C müşteri olup olmadığını döndürür.
     *
     * @param User|null $customer Kullanıcı (opsiyonel)
     * @return bool B2C ise true
     */
    public function isB2CCustomer(?User $customer = null): bool
    {
        return $this->detect($customer)->isB2C();
    }

    /**
     * Kullanıcının toptan (WHOLESALE) müşteri olup olmadığını döndürür.
     *
     * @param User|null $customer Kullanıcı (opsiyonel)
     * @return bool Wholesale ise true
     */
    public function isWholesaleCustomer(?User $customer = null): bool
    {
        return $this->detect($customer) === CustomerType::WHOLESALE;
    }

    /**
     * Kullanıcının bayi fiyatlarına erişip erişemeyeceğini döndürür.
     *
     * @param User|null $customer Kullanıcı (opsiyonel)
     * @return bool Erişebiliyorsa true
     */
    public function canAccessDealerPrices(?User $customer = null): bool
    {
        return $this->detect($customer)->canAccessDealerPrices();
    }

    /**
     * Kullanıcının müşteri katmanını (tier) döndürür.
     *
     * @param User|null $customer Kullanıcı (opsiyonel)
     * @return string Müşteri katmanı anahtarı
     */
    public function getCustomerTier(?User $customer = null): string
    {
        $customerType = $this->detect($customer);
        
        if (!$customer) {
            return 'guest';
        }

        // Sipariş geçmişi ve tipe göre müşteri katmanını belirle
        $totalOrders = $customer->orders()->completed()->count();
        $totalSpent = (float) ($customer->orders()->completed()->sum('total_amount') ?? 0);

        if ($customerType->isB2B()) {
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
}