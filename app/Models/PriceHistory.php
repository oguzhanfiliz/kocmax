<?php

namespace App\Models;

use App\Enums\Pricing\CustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    use HasFactory;

    // Disable updated_at since this is a historical record
    public const UPDATED_AT = null;

    protected $table = 'price_history';

    protected $fillable = [
        'product_variant_id',
        'customer_type',
        'old_price',
        'new_price',
        'old_cost',
        'new_cost',
        'reason',
        'notes',
        'metadata',
        'changed_by',
    ];

    protected $casts = [
        'customer_type' => CustomerType::class,
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'old_cost' => 'decimal:2',
        'new_cost' => 'decimal:2',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Product variant this price history belongs to
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * User who changed the price
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scope for specific customer type
     */
    public function scopeForCustomerType($query, CustomerType $customerType)
    {
        return $query->where('customer_type', $customerType);
    }

    /**
     * Scope for specific product variant
     */
    public function scopeForVariant($query, int $variantId)
    {
        return $query->where('product_variant_id', $variantId);
    }

    /**
     * Scope for recent changes
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for price increases
     */
    public function scopeIncreases($query)
    {
        return $query->whereRaw('new_price > old_price');
    }

    /**
     * Scope for price decreases
     */
    public function scopeDecreases($query)
    {
        return $query->whereRaw('new_price < old_price');
    }

    /**
     * Get price change amount
     */
    public function getPriceChangeAttribute(): float
    {
        return $this->new_price - $this->old_price;
    }

    /**
     * Get price change percentage
     */
    public function getPriceChangePercentageAttribute(): float
    {
        if ($this->old_price == 0) {
            return 0;
        }

        return (($this->new_price - $this->old_price) / $this->old_price) * 100;
    }

    /**
     * Get cost change amount
     */
    public function getCostChangeAttribute(): ?float
    {
        if (!$this->old_cost || !$this->new_cost) {
            return null;
        }

        return $this->new_cost - $this->old_cost;
    }

    /**
     * Get cost change percentage
     */
    public function getCostChangePercentageAttribute(): ?float
    {
        if (!$this->old_cost || !$this->new_cost || $this->old_cost == 0) {
            return null;
        }

        return (($this->new_cost - $this->old_cost) / $this->old_cost) * 100;
    }

    /**
     * Check if this is a price increase
     */
    public function isPriceIncrease(): bool
    {
        return $this->new_price > $this->old_price;
    }

    /**
     * Check if this is a price decrease
     */
    public function isPriceDecrease(): bool
    {
        return $this->new_price < $this->old_price;
    }

    /**
     * Check if this is a cost change
     */
    public function hasCostChange(): bool
    {
        return $this->old_cost !== null && 
               $this->new_cost !== null && 
               $this->old_cost != $this->new_cost;
    }

    /**
     * Get formatted price change
     */
    public function getFormattedPriceChange(): string
    {
        $change = $this->price_change;
        $symbol = $change > 0 ? '+' : '';
        
        return $symbol . number_format($change, 2) . ' ₺';
    }

    /**
     * Get formatted price change percentage
     */
    public function getFormattedPriceChangePercentage(): string
    {
        $percentage = $this->price_change_percentage;
        $symbol = $percentage > 0 ? '+' : '';
        
        return $symbol . number_format($percentage, 1) . '%';
    }

    /**
     * Create price history record
     */
    public static function createRecord(
        ProductVariant $variant,
        CustomerType $customerType,
        float $oldPrice,
        float $newPrice,
        ?float $oldCost = null,
        ?float $newCost = null,
        ?string $reason = null,
        ?string $notes = null,
        ?array $metadata = null,
        ?int $changedBy = null
    ): self {
        return static::create([
            'product_variant_id' => $variant->id,
            'customer_type' => $customerType,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'old_cost' => $oldCost,
            'new_cost' => $newCost,
            'reason' => $reason,
            'notes' => $notes,
            'metadata' => $metadata,
            'changed_by' => $changedBy ?? auth()->id(),
        ]);
    }
}