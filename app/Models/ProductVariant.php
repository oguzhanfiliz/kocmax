<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * Get variant display name
     */
    public function getDisplayNameAttribute()
    {
        // Simplified version - only use color/size fields
        $parts = array_filter([
            $this->color,
            $this->size,
            $this->name !== 'Varsayılan' ? $this->name : null
        ]);

        return empty($parts) ? 'Varsayılan' : implode(' - ', $parts);
    }

    /**
     * Get variant full name with product
     */
    public function getFullNameAttribute()
    {
        return $this->product->name . ' - ' . $this->display_name;
    }

    /**
     * Get variant price for specific user type
     */
    public function getPriceForUser($user = null)
    {
        // B2B kullanıcılar için özel fiyat mantığı burada olacak
        if ($user && $user->hasRole('dealer')) {
            // Dealer discount logic will be implemented here
            return $this->price; // Temporary
        }

        return $this->price;
    }

    /**
     * Update stock
     */
    public function updateStock($quantity, $operation = 'subtract')
    {
        if ($operation === 'subtract') {
            $this->stock = max(0, $this->stock - $quantity);
        } else {
            $this->stock += $quantity;
        }
        
        $this->save();
    }

    /**
     * Reserve stock
     */
    public function reserveStock($quantity)
    {
        if ($this->stock >= $quantity) {
            $this->updateStock($quantity, 'subtract');
            return true;
        }
        
        return false;
    }

    /**
     * Get variant image URL
     */
    public function getImageUrlAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // If no variant image, return product's primary image
        return $this->product->primaryImage?->image_url ?? '/images/no-image.png';
    }

    /**
     * Generate variant SKU
     */
    public function generateSku()
    {
        $baseSku = $this->product->sku;
        $suffix = '';

        // Use color/size fields for SKU
        if ($this->color) {
            $suffix .= '-' . strtoupper(substr($this->color, 0, 3));
        }

        if ($this->size) {
            $suffix .= '-' . strtoupper(str_replace([' ', '.'], '', $this->size));
        }

        if (empty($suffix)) {
            $suffix = '-VAR' . ($this->id ?? time());
        }

        return $baseSku . $suffix;
    }

    /**
     * Get attribute value by attribute ID
     * Simplified for performance
     */
    public function getAttributeValue($attributeId)
    {
        // For now, return null - this will be implemented later
        return null;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = $variant->generateSku();
            }
        });

        static::created(function ($variant) {
            // Update SKU after creation when ID is available
            if (str_contains($variant->sku, 'VAR')) {
                $variant->update(['sku' => $variant->generateSku()]);
            }
        });
    }
}