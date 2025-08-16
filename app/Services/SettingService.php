<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingService
{
    /**
     * Cache duration in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Get setting value with fallback.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Setting::getValue($key, $default);
        } catch (\Exception $e) {
            Log::error('Setting service error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Set setting value.
     */
    public function set(string $key, mixed $value, ?string $group = null): bool
    {
        try {
            return Setting::setValue($key, $value, $group);
        } catch (\Exception $e) {
            Log::error('Setting service set error', [
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get all settings for a specific group.
     */
    public function getGroup(string $group): array
    {
        try {
            return Setting::getGroup($group);
        } catch (\Exception $e) {
            Log::error('Setting service getGroup error', [
                'group' => $group,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Set multiple settings at once.
     */
    public function setMultiple(array $settings, ?string $group = null): bool
    {
        try {
            foreach ($settings as $key => $value) {
                if (!$this->set($key, $value, $group)) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Setting service setMultiple error', [
                'settings' => $settings,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get public settings for frontend.
     */
    public function getPublicSettings(): array
    {
        try {
            return Setting::getPublicSettings();
        } catch (\Exception $e) {
            Log::error('Setting service getPublicSettings error', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Clear all setting caches.
     */
    public function clearCache(): bool
    {
        try {
            Cache::flush(); // or specific pattern clearing
            return true;
        } catch (\Exception $e) {
            Log::error('Setting service clearCache error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Export settings to array (for backup/migration).
     */
    public function exportSettings(?string $group = null): array
    {
        try {
            $query = Setting::query();
            
            if ($group) {
                $query->where('group', $group);
            }
            
            return $query->get()
                ->map(function (Setting $setting) {
                    return [
                        'key' => $setting->key,
                        'value' => $setting->value,
                        'type' => $setting->type,
                        'group' => $setting->group,
                        'label' => $setting->label,
                        'description' => $setting->description,
                        'is_public' => $setting->is_public,
                        'validation_rules' => $setting->validation_rules,
                        'options' => $setting->options,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Setting service exportSettings error', [
                'group' => $group,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Import settings from array.
     */
    public function importSettings(array $settings): bool
    {
        try {
            foreach ($settings as $settingData) {
                $setting = Setting::firstOrNew(['key' => $settingData['key']]);
                $setting->fill($settingData);
                $setting->updated_by = auth()->id();
                $setting->save();
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Setting service importSettings error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Migrate config values to database settings.
     */
    public function migrateFromConfig(string $configKey, string $group = 'general'): bool
    {
        try {
            $configData = config($configKey, []);
            
            foreach ($configData as $key => $value) {
                $settingKey = "{$configKey}.{$key}";
                
                $setting = Setting::firstOrNew(['key' => $settingKey]);
                $setting->value = $value;
                $setting->type = Setting::inferType($value);
                $setting->group = $group;
                $setting->label = ucwords(str_replace('_', ' ', $key));
                $setting->created_by = auth()->id() ?? 1; // System user
                $setting->updated_by = auth()->id() ?? 1;
                $setting->save();
            }
            
            Log::info('Config migrated to settings', [
                'config_key' => $configKey,
                'group' => $group,
                'count' => count($configData)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Setting service migrateFromConfig error', [
                'config_key' => $configKey,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get pricing settings.
     */
    public function getPricingSettings(): array
    {
        return $this->getGroup('pricing');
    }

    /**
     * Get campaign settings.
     */
    public function getCampaignSettings(): array
    {
        return $this->getGroup('campaign');
    }

    /**
     * Get system settings.
     */
    public function getSystemSettings(): array
    {
        return $this->getGroup('system');
    }

    /**
     * Validate setting value.
     */
    public function validateSetting(string $key, mixed $value): array
    {
        try {
            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                return ['Setting not found'];
            }
            
            return $setting->validateValue($value);
        } catch (\Exception $e) {
            Log::error('Setting service validateSetting error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return ['Validation error'];
        }
    }

    /**
     * Get settings grouped by category for admin panel.
     */
    public function getGroupedSettings(): Collection
    {
        return Cache::remember('grouped_settings', self::CACHE_TTL, function () {
            return Setting::orderBy('group')
                ->orderBy('key')
                ->get()
                ->groupBy('group');
        });
    }
}
