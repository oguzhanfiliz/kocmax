<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class SettingHelper
{
    private static ?SettingService $settingService = null;

    private static function getService(): SettingService
    {
        if (self::$settingService === null) {
            self::$settingService = App::make(SettingService::class);
        }
        
        return self::$settingService;
    }

    /**
     * Get a setting value with fallback
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::getService()->get($key, $default);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value, ?string $group = null): bool
    {
        return self::getService()->set($key, $value, $group);
    }

    /**
     * Get pricing settings
     */
    public static function pricing(string $key = null): mixed
    {
        $settings = self::getService()->getPricingSettings();
        
        if ($key === null) {
            return $settings;
        }
        
        return $settings["pricing.$key"] ?? null;
    }

    /**
     * Get campaign settings
     */
    public static function campaign(string $key = null): mixed
    {
        $settings = self::getService()->getCampaignSettings();
        
        if ($key === null) {
            return $settings;
        }
        
        return $settings["campaign.$key"] ?? null;
    }

    /**
     * Get system settings
     */
    public static function system(string $key = null): mixed
    {
        $settings = self::getService()->getSystemSettings();
        
        if ($key === null) {
            return $settings;
        }
        
        return $settings["system.$key"] ?? null;
    }

    /**
     * Check if a feature is enabled
     */
    public static function isEnabled(string $feature): bool
    {
        return (bool) self::get($feature, false);
    }

    /**
     * Get all public settings (for frontend)
     */
    public static function getPublicSettings(): array
    {
        return self::getService()->getPublicSettings();
    }

    /**
     * Get settings statistics for admin dashboard
     */
    public static function getStats(): array
    {
        return Cache::remember('setting_stats', 300, function () {
            return [
                'total' => Setting::count(),
                'by_group' => Setting::selectRaw('`group`, COUNT(*) as count')
                    ->whereNotNull('group')
                    ->groupBy('group')
                    ->pluck('count', 'group')
                    ->toArray(),
                'public_count' => Setting::where('is_public', true)->count(),
                'encrypted_count' => Setting::where('is_encrypted', true)->count(),
                'last_updated' => Setting::orderBy('updated_at', 'desc')->first()?->updated_at,
            ];
        });
    }

    /**
     * Quick access to common settings
     */
    public static function siteName(): string
    {
        return self::get('system.site_name', 'E-Ticaret Sitesi');
    }

    public static function isMaintenanceMode(): bool
    {
        return self::get('system.maintenance_mode', false);
    }

    public static function defaultDealerDiscount(): float
    {
        return (float) self::get('pricing.default_dealer_discount', 15.0);
    }

    public static function freeShippingThreshold(): float
    {
        return (float) self::get('shipping.free_shipping_threshold', 500.0);
    }

    public static function standardShippingCost(): float
    {
        return (float) self::get('shipping.standard_shipping_cost', 25.0);
    }

    public static function defaultTaxRate(): float
    {
        return (float) self::get('pricing.default_tax_rate', 20.0);
    }

    public static function maxStackableCampaigns(): int
    {
        return (int) self::get('campaign.max_stackable_campaigns', 5);
    }

    public static function bulkDiscountTiers(): array
    {
        $tiers = self::get('pricing.bulk_discount_tiers', '{}');
        
        if (is_string($tiers)) {
            return json_decode($tiers, true) ?? [];
        }
        
        return is_array($tiers) ? $tiers : [];
    }

    /**
     * Validate setting value
     */
    public static function validateSetting(string $key, mixed $value): array
    {
        return self::getService()->validateSetting($key, $value);
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(): bool
    {
        return self::getService()->clearCache();
    }
}

// Global helper functions
if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Helpers\SettingHelper::get($key, $default);
    }
}

if (!function_exists('setting_enabled')) {
    function setting_enabled(string $feature): bool
    {
        return \App\Helpers\SettingHelper::isEnabled($feature);
    }
}
