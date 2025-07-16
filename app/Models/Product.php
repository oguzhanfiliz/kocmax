<?php

namespace App\Models;

use App\Services\SkuGeneratorService;
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = Str::slug($product->name) . '-' . uniqid();
            }
            
            // Auto-generate SKU if not provided
            if (empty($product->sku) && !empty($product->name)) {
                $skuGenerator = app(SkuGeneratorService::class);
                // İlişki kurulmadan önce category'lere erişilemeyebilir, bu yüzden slug'ı isimden alalım.
                $categorySlug = 'prod';
                $product->sku = $skuGenerator->generateSku($categorySlug, $product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . uniqid();
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function getCategoryIdsAttribute()
    {
        return $this->categories->pluck('id')->toArray();
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

    public function getPriceAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }

    public function getDiscountedPriceAttribute($value)
    {
        return $value ? number_format($value, 2, '.', '') : null;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->discounted_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->discounted_price || $this->discounted_price >= $this->price) {
            return 0;
        }
        
        return round((($this->price - $this->discounted_price) / $this->price) * 100);
    }

    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }

    public function isLowStock()
    {
        return $this->stock <= $this->min_stock_level;
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get product attribute values
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Get attributes through categories
     */
    public function availableAttributes()
    {
        $categoryIds = $this->categories()->pluck('categories.id');
        
        return ProductAttribute::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })
        ->active()
        ->orderBy('sort_order')
        ->get();
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
}
