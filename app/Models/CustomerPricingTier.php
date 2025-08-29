<?php

namespace App\Models;

use App\Enums\Pricing\CustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CustomerPricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'discount_percentage',
        'min_order_amount',
        'min_quantity',
        'description',
        'is_active',
        'priority',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'type' => CustomerType::class,
        'discount_percentage' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'min_quantity' => 'integer',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tier) {
            if (empty($tier->name)) {
                $tier->name = Str::title($tier->type->value) . ' Tier';
            }
        });
    }

    /**
     * Users assigned to this pricing tier
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'pricing_tier_id');
    }

    /**
     * Active pricing tiers
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
            });
    }

    /**
     * Filter by customer type
     */
    public function scopeForCustomerType($query, CustomerType $customerType)
    {
        return $query->where('type', $customerType);
    }

    /**
     * Order by priority
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('name');
    }

    /**
     * Check if tier is currently active
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

        return true;
    }

    /**
     * Check if user qualifies for this tier
     */
    public function qualifiesUser(User $user, float $orderAmount = 0, int $quantity = 1): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        // Check minimum order amount
        if ($this->min_order_amount > 0 && $orderAmount < $this->min_order_amount) {
            return false;
        }

        // Check minimum quantity
        if ($this->min_quantity > 0 && $quantity < $this->min_quantity) {
            return false;
        }

        // Check customer type compatibility
        $userCustomerType = app(\App\Services\Pricing\CustomerTypeDetector::class)->detect($user);
        
        // Allow flexible matching
        if ($this->type->isB2B() && !$userCustomerType->isB2B()) {
            return false;
        }

        if ($this->type->isB2C() && !$userCustomerType->isB2C()) {
            return false;
        }

        return true;
    }

    /**
     * Get the best tier for a user based on criteria
     */
    public static function getBestTierForUser(User $user, float $orderAmount = 0, int $quantity = 1): ?self
    {
        return static::active()
            ->ordered()
            ->get()
            ->first(fn($tier) => $tier->qualifiesUser($user, $orderAmount, $quantity));
    }

    /**
     * Calculate discount for given amount
     */
    public function calculateDiscount(float $amount): float
    {
        return $amount * ($this->discount_percentage / 100);
    }

    /**
     * Apply discount to amount
     */
    public function applyDiscount(float $amount): float
    {
        return $amount - $this->calculateDiscount($amount);
    }

    /**
     * Get tier statistics
     */
    public function getStats(): array
    {
        $userCount = $this->users()->count();
        $activeUserCount = $this->users()->whereNotNull('email_verified_at')->count();
        
        return [
            'total_users' => $userCount,
            'active_users' => $activeUserCount,
            'total_orders' => $this->getTotalOrders(),
            'total_revenue' => $this->getTotalRevenue(),
            'average_order_value' => $this->getAverageOrderValue(),
        ];
    }

    private function getTotalOrders(): int
    {
        return Order::whereHas('user', function ($query) {
                $query->where('pricing_tier_id', $this->id);
            })
            ->where('status', 'completed')
            ->count();
    }

    private function getTotalRevenue(): float
    {
        return (float) (Order::whereHas('user', function ($query) {
                $query->where('pricing_tier_id', $this->id);
            })
            ->where('status', 'completed')
            ->sum('total_amount') ?? 0);
    }

    private function getAverageOrderValue(): float
    {
        $totalOrders = $this->getTotalOrders();
        if ($totalOrders === 0) {
            return 0;
        }
        
        return $this->getTotalRevenue() / $totalOrders;
    }
}