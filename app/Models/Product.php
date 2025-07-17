<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sku',
        'barcode',
        'price',
        'discounted_price',
        'cost',
        'stock',
        'min_stock_level',
        'views',
        'weight',
        'dimensions',
        'is_active',
        'is_featured',
        'is_new',
        'is_bestseller',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:3',
        'dimensions' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_bestseller' => 'boolean',
        'stock' => 'integer',
        'min_stock_level' => 'integer',
        'views' => 'integer',
        'sort_order' => 'integer',
    ];

    // Boot fonksiyonu tamamen kaldırıldı - bellek sorunları nedeniyle

    public function categories(): BelongsToMany
    {
        // Minimalist relationship - bellek sorunları için
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function dealerDiscounts(): HasMany
    {
        return $this->hasMany(DealerDiscount::class);
    }

    public function bulkDiscounts(): HasMany
    {
        return $this->hasMany(BulkDiscount::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_products');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock_level');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function isLowStock()
    {
        return $this->stock <= $this->min_stock_level;
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Get product attribute values
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Get attribute value for a specific attribute
     */
    public function getAttributeValue($attributeId)
    {
        return $this->attributeValues()
            ->where('product_attribute_id', $attributeId)
            ->first()?->value;
    }

    /**
     * Set attribute value
     */
    public function setAttributeValue($attributeId, $value)
    {
        return $this->attributeValues()->updateOrCreate(
            ['product_attribute_id' => $attributeId],
            ['value' => $value]
        );
    }

    /**
     * Get current price (discounted or regular)
     */
    public function getCurrentPriceAttribute()
    {
        return $this->discounted_price ?? $this->price;
    }
}
