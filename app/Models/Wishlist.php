<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Wishlist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id',
        'notes',
        'added_at',
        'notification_sent_at',
        'is_favorite',
        'priority',
    ];

    protected $casts = [
        'added_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'is_favorite' => 'boolean',
    ];

    /**
     * Priority levels for wishlist items
     */
    public const PRIORITY_LOW = 1;
    public const PRIORITY_MEDIUM = 2;
    public const PRIORITY_HIGH = 3;
    public const PRIORITY_URGENT = 4;

    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    /**
     * Get the user that owns the wishlist item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with the wishlist item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant associated with the wishlist item
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Scope for user's wishlist items
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for favorite items
     */
    public function scopeFavorites(Builder $query): Builder
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope for items by priority
     */
    public function scopeByPriority(Builder $query, int $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for high priority items
     */
    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    /**
     * Scope for items needing notification
     */
    public function scopeNeedsNotification(Builder $query): Builder
    {
        return $query->whereNull('notification_sent_at')
                     ->where('created_at', '<=', now()->subHours(24));
    }

    /**
     * Check if item is available (in stock)
     */
    public function isAvailable(): bool
    {
        if ($this->product_variant_id) {
            return $this->productVariant->stock > 0;
        }
        
        return $this->product->variants()->where('stock', '>', 0)->exists();
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return self::getPriorities()[$this->priority] ?? 'Unknown';
    }

    /**
     * Get current price for the wishlist item
     */
    public function getCurrentPrice(): ?float
    {
        if ($this->product_variant_id) {
            return (float) $this->productVariant->price;
        }
        
        return (float) $this->product->base_price;
    }

    /**
     * Mark notification as sent
     */
    public function markNotificationSent(): void
    {
        $this->update(['notification_sent_at' => now()]);
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(): void
    {
        $this->update(['is_favorite' => !$this->is_favorite]);
    }

    /**
     * Check if user already has this item in wishlist
     */
    public static function existsForUser(int $userId, int $productId, ?int $variantId = null): bool
    {
        $query = static::forUser($userId)->where('product_id', $productId);
        
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->whereNull('product_variant_id');
        }
        
        return $query->exists();
    }

    /**
     * Create or update wishlist item
     */
    public static function addForUser(
        int $userId, 
        int $productId, 
        ?int $variantId = null, 
        ?string $notes = null,
        int $priority = self::PRIORITY_MEDIUM
    ): self {
        return static::updateOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
        ], [
            'notes' => $notes,
            'priority' => $priority,
            'added_at' => now(),
        ]);
    }
}