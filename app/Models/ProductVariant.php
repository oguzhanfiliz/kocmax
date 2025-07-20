<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'barcode',
        'price',
        'cost',
        'stock',
        'min_stock_level',
        'color',
        'size',
        'weight',
        'dimensions',
        'length',
        'width',
        'height',
        'is_active',
        'is_default',
        'sort_order',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:3',
        'dimensions' => 'array',
        'length' => 'decimal:1',
        'width' => 'decimal:1',
        'height' => 'decimal:1',
        'stock' => 'integer',
        'min_stock_level' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Variant belongs to a product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Variant has many cart items
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Variant has many order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Variant belongs to many variant options
     */
    public function variantOptions(): BelongsToMany
    {
        return $this->belongsToMany(VariantOption::class, 'product_variant_options')
            ->withTimestamps();
    }

    /**
     * Variant has many images
     */
    public function images(): HasMany
    {
        return $this->hasMany(VariantImage::class)->orderBy('sort_order');
    }

    /**
     * Variant has one primary image
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(VariantImage::class)->where('is_primary', true);
    }

    /**
     * Active variants scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * In stock variants scope
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Low stock variants scope
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock_level');
    }

    /**
     * Default variant scope
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Ordered variants scope
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Check if variant is low stock
     */
    public function isLowStock()
    {
        return $this->stock <= $this->min_stock_level;
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }
}