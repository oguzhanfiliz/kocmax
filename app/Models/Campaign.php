<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type', // 'buy_x_get_y_free', 'bundle_discount', 'cross_sell', etc.
        'status',
        'rules', // JSON: Campaign kuralları
        'rewards', // JSON: Hediye/indirim detayları
        'conditions', // JSON: Koşullar (min sepet tutarı vs)
        'priority',
        'is_active',
        'is_stackable', // Diğer kampanyalarla birleştirilebilir mi
        'starts_at',
        'ends_at',
        'usage_limit', // Toplam kullanım sınırı
        'usage_count', // Şu anki kullanım sayısı
        'usage_limit_per_customer', // Müşteri başına kullanım sınırı
        'minimum_cart_amount',
        'customer_types', // ['b2b', 'b2c', 'guest']
        'created_by',
        'updated_by'
    ];

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
        'minimum_cart_amount' => 'decimal:2'
    ];

    // Relations
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'campaign_products')
            ->withTimestamps();
    }

    public function triggerProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'campaign_trigger_products')
            ->withTimestamps();
    }

    public function rewardProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'campaign_reward_products')
            ->withPivot(['quantity', 'discount_percentage', 'fixed_discount'])
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'campaign_categories')
            ->withTimestamps();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CampaignUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeForCustomerType($query, string $customerType)
    {
        return $query->whereJsonContains('customer_types', $customerType)
            ->orWhereJsonLength('customer_types', 0);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Methods
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

    public function hasReachedUsageLimit(): bool
    {
        if (!$this->usage_limit) {
            return false;
        }

        return $this->usage_count >= $this->usage_limit;
    }

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

    public function isApplicableForCustomerType(string $customerType): bool
    {
        $allowedTypes = $this->customer_types ?? [];
        
        // Eğer customer_types boşsa, tüm müşteri tiplerine uygulanır
        if (empty($allowedTypes)) {
            return true;
        }

        return in_array($customerType, $allowedTypes);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function getProgressPercentage(): float
    {
        if (!$this->usage_limit) {
            return 0;
        }

        return min(($this->usage_count / $this->usage_limit) * 100, 100);
    }

    public function isUpcoming(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->starts_at && $this->starts_at->gt(now());
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->lt(now());
    }

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

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = \Str::slug($model->name);
            }
            
            // Set created_by if user is authenticated and field is empty
            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = \Str::slug($model->name);
            }
            
            // Set updated_by if user is authenticated
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
