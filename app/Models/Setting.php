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

    protected $hidden = [
        'value', // Value accessor'la kontrol edilecek
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

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
            $value = Crypt::decryptString($value);
        }

        // Cast to appropriate type
        return match ($this->type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array', 'json' => json_decode($value, true) ?: [],
            default => $value,
        };
    }

    /**
     * Set the encrypted/serialized value.
     */
    public function setValueAttribute(mixed $value): void
    {
        // Serialize complex types
        if (in_array($this->type, ['array', 'json'])) {
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
            $setting = static::where('key', $key)->first();
            return $setting?->value ?? $default;
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
}
