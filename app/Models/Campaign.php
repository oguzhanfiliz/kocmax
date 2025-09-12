<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Kampanya modeli.
 *
 * Kampanyaların kuralları (rules), ödülleri (rewards), koşulları (conditions),
 * hedeflediği müşteri tipleri ve kullanım limitleri gibi bilgiler bu modelde tutulur.
 */
class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Toplu atamaya izin verilen alanlar.
     *
     * Not: JSON alanlar (rules, rewards, conditions) kampanya kuralları ve ödülleri içerir.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type', // 'buy_x_get_y_free', 'bundle_discount', 'cross_sell' vb.
        'status',
        'rules', // JSON: Kampanya kuralları
        'rewards', // JSON: Hediye/indirim detayları
        'conditions', // JSON: Koşullar (min sepet tutarı vb.)
        'priority',
        'is_active',
        'is_stackable', // Diğer kampanyalarla birleştirilebilir mi?
        'starts_at',
        'ends_at',
        'usage_limit', // Toplam kullanım sınırı
        'usage_count', // Şu anki kullanım sayısı
        'usage_limit_per_customer', // Müşteri başına kullanım sınırı
        'minimum_cart_amount',
        'customer_types', // ['b2b', 'b2c', 'guest']
        'created_by',
        'updated_by',
        // Kampanya türüne özel alanlar
        'required_quantity',
        'free_quantity',
        'bundle_discount_type',
        'bundle_discount_value',
        'free_shipping_min_amount',
        'flash_discount_type',
        'flash_discount_value'
    ];

    /**
     * Alan dönüşümleri.
     *
     * Bazı alanlar JSON ve tarih tipine, sayısal alanlar ise uygun sayısal tiplere çevrilir.
     */
    protected $casts = [
        'rules' => 'array',
        'rewards' => 'array',
        'conditions' => 'array',
        'customer_types' => 'array',
        'is_active' => 'boolean',
        'is_stackable' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'priority' => 'integer',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'minimum_cart_amount' => 'decimal:2',
        // Kampanya türüne özel alanlar
        'required_quantity' => 'integer',
        'free_quantity' => 'integer',
        'bundle_discount_value' => 'decimal:2',
        'free_shipping_min_amount' => 'decimal:2',
        'flash_discount_value' => 'decimal:2'
    ];

    /**
     * İlişkiler
     */

    /**
     * Kampanyanın dahil olduğu ürünler.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'campaign_products')
            ->withTimestamps();
    }

    /**
     * Kampanyayı tetikleyen ürünler.
     */
    public function triggerProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'campaign_trigger_products')
            ->withTimestamps();
    }

    /**
     * Kampanya kapsamında ödül/indirim uygulanacak ürünler.
     */
    public function rewardProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'campaign_reward_products')
            ->withPivot(['quantity', 'discount_percentage', 'fixed_discount'])
            ->withTimestamps();
    }

    /**
     * Kampanyanın hedeflediği kategoriler.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'campaign_categories')
            ->withTimestamps();
    }

    /**
     * Kampanya kullanım kayıtları.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CampaignUsage::class);
    }

    /**
     * Sorgu kapsamları (Scopes)
     */

    /**
     * Aktif kampanyalar (tarih aralığı ve is_active bayrağına göre).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * Müşteri tipine uygun kampanyalar.
     */
    public function scopeForCustomerType($query, string $customerType)
    {
        return $query->whereJsonContains('customer_types', $customerType)
            ->orWhereJsonLength('customer_types', 0);
    }

    /**
     * Kampanya türüne göre filtreleme.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Yardımcı metodlar
     */

    /**
     * Kampanya aktif mi?
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Toplam kullanım limiti aşıldı mı?
     */
    public function hasReachedUsageLimit(): bool
    {
        if (!$this->usage_limit) {
            return false;
        }

        return $this->usage_count >= $this->usage_limit;
    }

    /**
     * Belirli bir kullanıcı kampanyayı kullanabilir mi?
     */
    public function canBeUsedBy(User $user): bool
    {
        if (!$this->usage_limit_per_customer) {
            return true;
        }

        $userUsageCount = $this->usages()
            ->where('user_id', $user->id)
            ->count();

        return $userUsageCount < $this->usage_limit_per_customer;
    }

    /**
     * Müşteri tipi bazında uygulanabilirlik kontrolü.
     */
    public function isApplicableForCustomerType(string $customerType): bool
    {
        $allowedTypes = $this->customer_types ?? [];
        
        // Eğer customer_types boşsa, tüm müşteri tiplerine uygulanır
        if (empty($allowedTypes)) {
            return true;
        }

        return in_array($customerType, $allowedTypes);
    }

    /**
     * Kullanım sayısını bir artır.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Kullanım ilerleme yüzdesi (0-100 arası).
     */
    public function getProgressPercentage(): float
    {
        if (!$this->usage_limit) {
            return 0;
        }

        return min(($this->usage_count / $this->usage_limit) * 100, 100);
    }

    /**
     * Kampanya ileri tarihli (başlamadı) ve aktif mi?
     */
    public function isUpcoming(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->starts_at && $this->starts_at->gt(now());
    }

    /**
     * Kampanya süresi dolmuş mu?
     */
    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->lt(now());
    }

    /**
     * Kalan gün sayısını hesaplayan erişimci (accessor).
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->ends_at) {
            return null;
        }

        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->ends_at);
    }

    /**
     * Model olaylarını (creating/updating) dinleyen boot metodu.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = \Str::slug($model->name);
            }
            
            // Kullanıcı doğrulanmışsa created_by ve updated_by alanlarını ayarla
            if (auth()->check()) {
                if (empty($model->created_by)) {
                    $model->created_by = auth()->id();
                }
                if (empty($model->updated_by)) {
                    $model->updated_by = auth()->id();
                }
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = \Str::slug($model->name);
            }
            
            // Kullanıcı doğrulanmışsa updated_by alanını güncelle
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
