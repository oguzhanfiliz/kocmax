<?php

namespace App\Models;

use App\Enums\Pricing\CustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PricingRule extends Model
{
    use HasFactory;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED_AMOUNT = 'fixed_amount';
    public const TYPE_TIERED = 'tiered';
    public const TYPE_BULK = 'bulk';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'conditions',
        'actions',
        'priority',
        'is_active',
        'is_stackable',
        'is_exclusive',
        'starts_at',
        'ends_at',
        'usage_limit',
        'usage_count',
        'usage_limit_per_customer',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'priority' => 'integer',
        'is_active' => 'boolean',
        'is_stackable' => 'boolean',
        'is_exclusive' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'usage_limit_per_customer' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rule) {
            if (empty($rule->slug)) {
                $rule->slug = Str::slug($rule->name);
            }
        });

        static::updating(function ($rule) {
            if ($rule->isDirty('name') && empty($rule->slug)) {
                $rule->slug = Str::slug($rule->name);
            }
        });
    }

    /**
     * User who created the rule
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated the rule
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Products this rule applies to
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'pricing_rule_products');
    }

    /**
     * Categories this rule applies to
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'pricing_rule_categories');
    }

    /**
     * Active pricing rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereRaw('usage_count < usage_limit');
            });
    }

    /**
     * Rules by priority
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'asc');
    }

    /**
     * Rules applicable to a customer type
     */
    public function scopeForCustomerType($query, CustomerType $customerType)
    {
        return $query->whereJsonContains('conditions->customer_type', $customerType->value)
            ->orWhereJsonContains('conditions->customer_types', $customerType->value)
            ->orWhereNull('conditions->customer_type');
    }

    /**
     * Rules applicable to a quantity
     */
    public function scopeForQuantity($query, int $quantity)
    {
        return $query->where(function ($q) use ($quantity) {
            $q->whereNull('conditions->min_quantity')
                ->orWhereJsonExtract('conditions', '$.min_quantity') <= $quantity;
        });
    }

    /**
     * Rules applicable to a product
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where(function ($q) use ($productId) {
            $q->whereHas('products', function ($productQuery) use ($productId) {
                $productQuery->where('products.id', $productId);
            })
            ->orWhereHas('categories', function ($categoryQuery) use ($productId) {
                $categoryQuery->whereHas('products', function ($productQuery) use ($productId) {
                    $productQuery->where('products.id', $productId);
                });
            })
            ->orWhere(function ($generalQuery) {
                // Rules that apply to all products (no specific product/category restrictions)
                $generalQuery->doesntHave('products')->doesntHave('categories');
            });
        });
    }

    /**
     * Check if rule is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if rule applies to given conditions
     */
    public function appliesTo(array $context): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $conditions = $this->conditions ?? [];

        // Check customer type
        if (isset($conditions['customer_type']) && isset($context['customer_type'])) {
            if ($conditions['customer_type'] !== $context['customer_type']) {
                return false;
            }
        }

        if (isset($conditions['customer_types']) && isset($context['customer_type'])) {
            if (!in_array($context['customer_type'], $conditions['customer_types'])) {
                return false;
            }
        }

        // Check minimum quantity
        if (isset($conditions['min_quantity']) && isset($context['quantity'])) {
            if ($context['quantity'] < $conditions['min_quantity']) {
                return false;
            }
        }

        // Check minimum amount
        if (isset($conditions['min_amount']) && isset($context['amount'])) {
            if ($context['amount'] < $conditions['min_amount']) {
                return false;
            }
        }

        // Check maximum quantity
        if (isset($conditions['max_quantity']) && isset($context['quantity'])) {
            if ($context['quantity'] > $conditions['max_quantity']) {
                return false;
            }
        }

        // Check day of week
        if (isset($conditions['days_of_week'])) {
            $currentDay = now()->dayOfWeek; // 0 = Sunday
            if (!in_array($currentDay, $conditions['days_of_week'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount based on rule
     */
    public function calculateDiscount(float $baseAmount, array $context = []): float
    {
        if (!$this->appliesTo($context)) {
            return 0;
        }

        $actions = $this->actions ?? [];

        return match($this->type) {
            self::TYPE_PERCENTAGE => $this->calculatePercentageDiscount($baseAmount, $actions),
            self::TYPE_FIXED_AMOUNT => $this->calculateFixedAmountDiscount($baseAmount, $actions),
            self::TYPE_TIERED => $this->calculateTieredDiscount($baseAmount, $actions, $context),
            self::TYPE_BULK => $this->calculateBulkDiscount($baseAmount, $actions, $context),
            default => 0,
        };
    }

    private function calculatePercentageDiscount(float $baseAmount, array $actions): float
    {
        $percentage = $actions['discount_percentage'] ?? 0;
        return $baseAmount * ($percentage / 100);
    }

    private function calculateFixedAmountDiscount(float $baseAmount, array $actions): float
    {
        $amount = $actions['discount_amount'] ?? 0;
        return min($amount, $baseAmount); // Don't discount more than the base amount
    }

    private function calculateTieredDiscount(float $baseAmount, array $actions, array $context): float
    {
        $tiers = $actions['tiers'] ?? [];
        $quantity = $context['quantity'] ?? 1;

        $applicableTier = null;
        foreach ($tiers as $tier) {
            if (($tier['min_quantity'] ?? 0) <= $quantity) {
                $applicableTier = $tier;
            }
        }

        if (!$applicableTier) {
            return 0;
        }

        if (isset($applicableTier['discount_percentage'])) {
            return $baseAmount * ($applicableTier['discount_percentage'] / 100);
        }

        if (isset($applicableTier['discount_amount'])) {
            return min($applicableTier['discount_amount'], $baseAmount);
        }

        return 0;
    }

    private function calculateBulkDiscount(float $baseAmount, array $actions, array $context): float
    {
        $quantity = $context['quantity'] ?? 1;
        $minQuantity = $actions['min_quantity'] ?? 1;

        if ($quantity < $minQuantity) {
            return 0;
        }

        if (isset($actions['discount_percentage'])) {
            return $baseAmount * ($actions['discount_percentage'] / 100);
        }

        if (isset($actions['discount_per_item'])) {
            return min($actions['discount_per_item'] * $quantity, $baseAmount);
        }

        return 0;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentage(): float
    {
        if (!$this->usage_limit) {
            return 0;
        }

        return ($this->usage_count / $this->usage_limit) * 100;
    }

    /**
     * Check if rule has usage remaining
     */
    public function hasUsageRemaining(): bool
    {
        if (!$this->usage_limit) {
            return true;
        }

        return $this->usage_count < $this->usage_limit;
    }
}