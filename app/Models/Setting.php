<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
        'is_encrypted',
        'validation_rules',
        'options',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_encrypted' => 'boolean',
        'validation_rules' => 'array',
        'options' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Essential settings that should never be deleted.
     */
    public const ESSENTIAL_SETTINGS = [
        'site_title', 'site_description', 'site_logo', 'site_favicon',
        'contact_phone', 'contact_email', 'contact_address',
        'company_name', 'company_tax_number',
        'social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin',
        'theme_color', 'enable_dark_mode', 'enable_product_reviews'
    ];

    protected $hidden = [
        // Value alanını hidden'dan çıkar çünkü Filament form'da kullanılıyor
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Essential ayarların silinmesini engelle
        static::deleting(function (Setting $setting): bool {
            if (in_array($setting->key, self::ESSENTIAL_SETTINGS)) {
                throw new \Exception("Essential setting '{$setting->key}' cannot be deleted.");
            }
            return true;
        });

        // Cache'i temizle
        static::saved(function (Setting $setting): void {
            Cache::forget("setting_{$setting->key}");
            Cache::forget("settings_group_{$setting->group}");
            Cache::forget('all_settings');
        });

        static::deleted(function (Setting $setting): void {
            Cache::forget("setting_{$setting->key}");
            Cache::forget("settings_group_{$setting->group}");
            Cache::forget('all_settings');
        });
    }

    /**
     * Get the creator of this setting.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the last updater of this setting.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the decrypted/cast value.
     */
    public function getValueAttribute(?string $value): mixed
    {
        if ($value === null) {
            return null;
        }

        // Decrypt if needed
        if ($this->is_encrypted) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                // Eğer decrypt edilemezse raw değeri döndür
                return $value;
            }
        }

        // Sadece özel durumlar için casting yap
        switch ($this->type) {
            case 'boolean':
                // Sadece boolean tip ayarlar için ve sadece tam eşleşme kontrolü
                $lowerValue = strtolower(trim($value));
                if ($lowerValue === '1' || $lowerValue === 'true' || $lowerValue === 'on' || $lowerValue === 'yes') {
                    return true;
                } elseif ($lowerValue === '0' || $lowerValue === 'false' || $lowerValue === 'off' || $lowerValue === 'no' || $lowerValue === '') {
                    return false;
                }
                // Diğer tüm değerler için raw string döndür
                return $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'json':
                return json_decode($value, true) ?: [];
            default:
                // String ve diğer tüm tipler için raw değeri döndür
                $processedValue = $value;
                
                // Copyright için özel işlem
                if ($this->key === 'copyright_text' && is_string($processedValue)) {
                    $processedValue = str_replace('{year}', date('Y'), $processedValue);
                }
                
                return $processedValue;
        }
    }

    /**
     * Set the encrypted/serialized value.
     */
    public function setValueAttribute(mixed $value): void
    {
        // Boolean değerler için özel işlem
        if ($this->type === 'boolean') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        }
        // Serialize complex types
        elseif (in_array($this->type, ['array', 'json'])) {
            $value = json_encode($value);
        } else {
            $value = (string) $value;
        }

        // Encrypt if needed
        if ($this->is_encrypted) {
            $value = Crypt::encryptString($value);
        }

        $this->attributes['value'] = $value;
    }

    /**
     * Get a setting value by key with caching.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            try {
                $setting = static::where('key', $key)->first();
                $value = $setting?->value ?? $default;
                
                // Ensure array values are properly formatted
                if (is_array($value)) {
                    return $value;
                }
                
                return $value;
            } catch (\Exception $e) {
                \Log::warning("Error getting setting {$key}: " . $e->getMessage());
                return $default;
            }
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, mixed $value, ?string $group = null): bool
    {
        $setting = static::firstOrNew(['key' => $key]);
        
        if (!$setting->exists) {
            $setting->group = $group ?? 'general';
            $setting->type = static::inferType($value);
        }
        
        $setting->value = $value;
        $setting->updated_by = auth()->id();
        
        return $setting->save();
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            return static::where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Get all public settings (for frontend).
     */
    public static function getPublicSettings(): array
    {
        return Cache::remember('public_settings', 3600, function () {
            return static::where('is_public', true)
                ->select('key', 'value', 'type')
                ->get()
                ->mapWithKeys(function (Setting $setting) {
                    return [$setting->key => $setting->value];
                })
                ->toArray();
        });
    }

    /**
     * Infer the type from a value.
     */
    private static function inferType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'array',
            default => 'string',
        };
    }

    /**
     * Validate setting value against rules.
     */
    public function validateValue(mixed $value): array
    {
        if (empty($this->validation_rules)) {
            return [];
        }

        $validator = validator(['value' => $value], ['value' => $this->validation_rules]);
        
        return $validator->errors()->get('value') ?? [];
    }

    /**
     * Scope to public settings only.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to specific group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Check if this setting is essential.
     */
    public function isEssential(): bool
    {
        return in_array($this->key, self::ESSENTIAL_SETTINGS);
    }

    /**
     * Create essential settings if they don't exist.
     */
    public static function createEssentialSettings(): void
    {
        $essentialSettings = [
            ['key' => 'site_title', 'label' => 'Site Başlığı', 'value' => 'E-Ticaret Sitesi', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'label' => 'Site Açıklaması', 'value' => 'Modern e-ticaret platformu', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_logo', 'label' => 'Site Logosu', 'value' => '', 'type' => 'image', 'group' => 'general'],
            ['key' => 'site_favicon', 'label' => 'Site Favicon', 'value' => '/images/favicon.ico', 'type' => 'image', 'group' => 'general'],
            ['key' => 'contact_phone', 'label' => 'İletişim Telefonu', 'value' => '', 'type' => 'string', 'group' => 'contact'],
            ['key' => 'contact_email', 'label' => 'İletişim E-posta', 'value' => '', 'type' => 'string', 'group' => 'contact'],
            ['key' => 'contact_address', 'label' => 'İletişim Adresi', 'value' => '', 'type' => 'string', 'group' => 'contact'],
            ['key' => 'company_name', 'label' => 'Şirket Adı', 'value' => '', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_tax_number', 'label' => 'Vergi Numarası', 'value' => '', 'type' => 'string', 'group' => 'company'],
            ['key' => 'social_facebook', 'label' => 'Facebook URL', 'value' => '', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_twitter', 'label' => 'Twitter URL', 'value' => '', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_instagram', 'label' => 'Instagram URL', 'value' => '', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_linkedin', 'label' => 'LinkedIn URL', 'value' => '', 'type' => 'string', 'group' => 'social'],
            ['key' => 'theme_color', 'label' => 'Tema Rengi', 'value' => '#3b82f6', 'type' => 'string', 'group' => 'ui'],
            ['key' => 'enable_dark_mode', 'label' => 'Koyu Tema', 'value' => false, 'type' => 'boolean', 'group' => 'ui'],
            ['key' => 'enable_product_reviews', 'label' => 'Ürün Yorumları', 'value' => true, 'type' => 'boolean', 'group' => 'features'],
        ];

        foreach ($essentialSettings as $settingData) {
            self::firstOrCreate(
                ['key' => $settingData['key']],
                array_merge($settingData, [
                    'description' => "Essential setting: {$settingData['label']}",
                    'is_public' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                ])
            );
        }
    }
}
