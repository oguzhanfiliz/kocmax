<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'barcode',
        'base_price',
        'base_currency',
        'weight',
        'box_quantity',
        'product_weight',
        'package_quantity',
        'package_weight',
        'package_length',
        'package_width',
        'package_height',
        'is_active',
        'is_featured',
        'is_new',
        'is_bestseller',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'sort_order',
        'gender',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'box_quantity' => 'integer',
        'product_weight' => 'decimal:3',
        'package_quantity' => 'integer',
        'package_weight' => 'decimal:3',
        'package_length' => 'decimal:1',
        'package_width' => 'decimal:1',
        'package_height' => 'decimal:1',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_bestseller' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = static::generateSku($product);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::saved(function ($product) {
            // Filament form submission'dan sonra kategorileri kontrol et
            if (request()->has('categories') || isset($product->getAttributes()['categories'])) {
                $categories = request('categories') ?? $product->categories->pluck('id')->toArray();
                if (!empty($categories)) {
                    $product->validateAndSyncCategories($categories);
                }
            }
        });
    }

    /**
     * Generate SKU for product
     */
    private static function generateSku($product)
    {
        $prefix = 'PRD';
        $timestamp = now()->format('ymd');
        $random = strtoupper(Str::random(3));
        
        return "{$prefix}-{$timestamp}-{$random}";
    }

    // Brand sistemi kaldırıldı - VariantType olarak kullanılacak

    /**
     * Scope to find by ID or slug
     */
    public function scopeFindByIdOrSlug($query, $identifier)
    {
        return $query->where(function ($q) use ($identifier) {
            $q->where('id', $identifier)
              ->orWhere('slug', $identifier);
        });
    }

    /**
     * Product belongs to many categories
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id')
            ->select(['categories.id', 'categories.name', 'categories.slug'])
            ->withPivot('id');
    }

    /**
     * Product has many variants
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Product has many images
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Product has one primary image
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Product has many reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Product has many certificates
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(ProductCertificate::class)->ordered();
    }

    /**
     * Product has many active certificates
     */
    public function activeCertificates(): HasMany
    {
        return $this->hasMany(ProductCertificate::class)->active()->ordered();
    }

    /**
     * Product has many approved reviews
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    // ProductAttribute sistemi kaldırıldı - Variant sistemi kullanılacak

    /**
     * Product belongs to many campaigns
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_products');
    }

    /**
     * Product has many cart items
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Product has many order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Active products scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Featured products scope
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * New products scope
     */
    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    /**
     * Bestseller products scope
     */
    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    /**
     * Products with stock scope
     */
    public function scopeInStock($query)
    {
        return $query->whereHas('variants', function ($query) {
            $query->where('stock', '>', 0);
        });
    }

    /**
     * Ordered products scope
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }


    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get total stock from all variants
     * Disabled to prevent memory issues
     */
    // public function getTotalStockAttribute()
    // {
    //     return \Cache::remember("product_total_stock_{$this->id}", 300, function () {
    //         return $this->variants()->sum('stock');
    //     });
    // }

    /**
     * Get minimum price from variants
     * Disabled to prevent memory issues
     */
    // public function getMinPriceAttribute()
    // {
    //     return \Cache::remember("product_min_price_{$this->id}", 300, function () {
    //         return $this->variants()->min('price') ?? $this->base_price;
    //     });
    // }

    /**
     * Get maximum price from variants
     * Disabled to prevent memory issues
     */
    // public function getMaxPriceAttribute()
    // {
    //     return \Cache::remember("product_max_price_{$this->id}", 300, function () {
    //         return $this->variants()->max('price') ?? $this->base_price;
    //     });
    // }

    /**
     * Get price range string
     * Disabled to prevent memory issues
     */
    // public function getPriceRangeAttribute()
    // {
    //     $min = $this->min_price;
    //     $max = $this->max_price;

    //     if ($min == $max) {
    //         return number_format($min, 2) . ' ₺';
    //     }

    //     return number_format($min, 2) . ' - ' . number_format($max, 2) . ' ₺';
    // }

    /**
     * Check if product has multiple variants
     */
    public function hasVariants()
    {
        return $this->variants()->count() > 1;
    }

    /**
     * Check if product is in stock
     * Simplified to prevent memory issues
     */
    public function isInStock()
    {
        return $this->variants()->where('stock', '>', 0)->exists();
    }

    /**
     * Get available colors
     * Disabled to prevent memory issues
     */
    // public function getAvailableColors()
    // {
    //     return \Cache::remember("product_colors_{$this->id}", 600, function () {
    //         return $this->variants()
    //             ->select('color')
    //             ->whereNotNull('color')
    //             ->distinct()
    //             ->pluck('color')
    //             ->filter();
    //     });
    // }

    /**
     * Get available sizes
     * Disabled to prevent memory issues
     */
    // public function getAvailableSizes()
    // {
    //     return \Cache::remember("product_sizes_{$this->id}", 600, function () {
    //         return $this->variants()
    //             ->select('size')
    //             ->whereNotNull('size')
    //             ->distinct()
    //             ->pluck('size')
    //             ->filter();
    //     });
    // }

    // getAvailableAttributeValues kaldırıldı - Variant sistemi kullanılacak

    // getAttributeValue kaldırıldı - Variant sistemi kullanılacak

    /**
     * Get average rating
     * Disabled to prevent memory issues
     */
    // public function getAverageRatingAttribute()
    // {
    //     return $this->approvedReviews()->avg('rating') ?? 0;
    // }

    /**
     * Get reviews count
     * Disabled to prevent memory issues
     */
    // public function getReviewsCountAttribute()
    // {
    //     return $this->approvedReviews()->count();
    // }

    /**
     * Create default variant if product doesn't have any
     */
    public function createDefaultVariant()
    {
        if ($this->variants()->count() === 0) {
            $this->variants()->create([
                'name' => 'Varsayılan',
                'sku' => $this->sku . '-DEFAULT',
                'price' => $this->base_price,
                'stock' => 0,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Get primary category
     * Disabled to prevent memory issues
     */
    // public function getPrimaryCategoryAttribute()
    // {
    //     return $this->categories()->orderBy('product_categories.id')->first();
    // }

    /**
     * Search products by term
     */
    public function scopeSearch($query, $term)
    {
        if (empty(trim($term))) {
            return $query;
        }

        // Türkçe karakter desteği için normalize
        $normalizedTerm = $this->normalizeSearchTerm($term);
        
        return $query->where(function ($q) use ($normalizedTerm) {
            // Ana ürün bilgilerinde ara (MySQL için LIKE, PostgreSQL için ILIKE)
            $q->where('name', 'LIKE', "%{$normalizedTerm}%")
              ->orWhere('description', 'LIKE', "%{$normalizedTerm}%")
              ->orWhere('short_description', 'LIKE', "%{$normalizedTerm}%")
              ->orWhere('sku', 'LIKE', "%{$normalizedTerm}%")
              ->orWhere('barcode', 'LIKE', "%{$normalizedTerm}%")
              ->orWhere('slug', 'LIKE', "%{$normalizedTerm}%")
              
              // Kategori isimlerinde ara
              ->orWhereHas('categories', function ($cat) use ($normalizedTerm) {
                  $cat->where('name', 'LIKE', "%{$normalizedTerm}%")
                      ->orWhere('slug', 'LIKE', "%{$normalizedTerm}%");
              })
              
              // Ürün varyantlarında ara
              ->orWhereHas('variants', function ($variant) use ($normalizedTerm) {
                  $variant->where('color', 'LIKE', "%{$normalizedTerm}%")
                          ->orWhere('size', 'LIKE', "%{$normalizedTerm}%")
                          ->orWhere('sku', 'LIKE', "%{$normalizedTerm}%");
              });
        });
    }

    /**
     * Normalize search term for Turkish characters
     */
    private function normalizeSearchTerm(string $search): string
    {
        $search = trim($search);
        $search = mb_strtolower($search, 'UTF-8');
        
        // Çoklu boşlukları tek boşluğa çevir
        $search = preg_replace('/\s+/', ' ', $search);
        
        return $search;
    }

    /**
     * Filter by category
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        });
    }

    /**
     * Filter by price range
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereHas('variants', function ($query) use ($min, $max) {
            $query->whereBetween('price', [$min, $max]);
        });
    }

    // scopeHasAttributeValue kaldırıldı - Variant sistemi kullanılacak

    /**
     * Validate and sync categories with parent requirement
     */
    public function validateAndSyncCategories($categoryIds)
    {
        if (empty($categoryIds) || !is_array($categoryIds)) {
            return;
        }

        $allRequiredCategories = collect();

        foreach ($categoryIds as $categoryId) {
            $category = Category::find($categoryId);
            if (!$category) {
                continue;
            }

            // Add the category itself
            $allRequiredCategories->push($categoryId);

            // Add all parent categories
            $parent = $category->parent;
            $visited = [$categoryId];
            $depth = 0;

            while ($parent && $depth < 10) {
                if (in_array($parent->id, $visited)) {
                    break; // Prevent infinite loop
                }
                $visited[] = $parent->id;
                $allRequiredCategories->push($parent->id);
                $parent = $parent->parent;
                $depth++;
            }
        }

        // Sync with all required categories (including parents)
        $this->categories()->sync($allRequiredCategories->unique()->values()->toArray());
    }

    /**
     * Paket boyutları bilgilerini al (varyanttan önce ürün seviyesinden)
     */
    public function getPackageDimensions(): array
    {
        return [
            'box_quantity' => $this->box_quantity,
            'product_weight' => $this->product_weight,
            'package_quantity' => $this->package_quantity,
            'package_weight' => $this->package_weight,
            'package_length' => $this->package_length,
            'package_width' => $this->package_width,
            'package_height' => $this->package_height,
            'package_size' => $this->getPackageSizeFormatted(),
        ];
    }

    /**
     * Koli ölçülerini formatla (53x40x41 cm)
     */
    public function getPackageSizeFormatted(): ?string
    {
        if (!$this->package_length || !$this->package_width || !$this->package_height) {
            return null;
        }

        return "{$this->package_length}x{$this->package_width}x{$this->package_height} cm";
    }

    /**
     * Tüm varyantlara ürün seviyesi paket boyutlarını uygula
     */
    public function applyPackageDimensionsToAllVariants(): void
    {
        $packageData = [
            'box_quantity' => $this->box_quantity,
            'product_weight' => $this->product_weight,
            'package_quantity' => $this->package_quantity,
            'package_weight' => $this->package_weight,
            'package_length' => $this->package_length,
            'package_width' => $this->package_width,
            'package_height' => $this->package_height,
        ];

        // Sadece null olan alanları güncelle (varyant seviyesindeki özelleştirmeler korunsun)
        $this->variants()->chunk(100, function ($variants) use ($packageData) {
            foreach ($variants as $variant) {
                $updateData = [];
                foreach ($packageData as $field => $value) {
                    if (is_null($variant->$field) && !is_null($value)) {
                        $updateData[$field] = $value;
                    }
                }
                if (!empty($updateData)) {
                    $variant->update($updateData);
                }
            }
        });
    }

    /**
     * İlgili PackageIconsHelper ikonlarıyla paket boyutlarını döndür
     */
    public function getPackageDimensionsWithIcons(): array
    {
        $dimensions = $this->getPackageDimensions();
        $iconHelper = \App\Helpers\PackageIconsHelper::class;
        
        return [
            'box_quantity' => [
                'value' => $dimensions['box_quantity'],
                'icon' => $iconHelper::getBoxQuantityIcon(),
                'label' => 'Kutu Adeti',
                'formatted' => $dimensions['box_quantity'] ? $dimensions['box_quantity'] . ' Adet' : null,
            ],
            'product_weight' => [
                'value' => $dimensions['product_weight'],
                'icon' => $iconHelper::getProductWeightIcon(),
                'label' => 'Ürün Ağırlığı',
                'formatted' => $dimensions['product_weight'] ? $dimensions['product_weight'] . ' gr.' : null,
            ],
            'package_quantity' => [
                'value' => $dimensions['package_quantity'],
                'icon' => $iconHelper::getPackageQuantityIcon(),
                'label' => 'Koli Adeti',
                'formatted' => $dimensions['package_quantity'] ? $dimensions['package_quantity'] . ' Adet' : null,
            ],
            'package_weight' => [
                'value' => $dimensions['package_weight'],
                'icon' => $iconHelper::getPackageWeightIcon(),
                'label' => 'Koli Ağırlığı',
                'formatted' => $dimensions['package_weight'] ? $dimensions['package_weight'] . ' Kg.' : null,
            ],
            'package_size' => [
                'value' => $dimensions['package_size'],
                'icon' => $iconHelper::getPackageSizeIcon(),
                'label' => 'Koli Ölçüsü',
                'formatted' => $dimensions['package_size'],
            ],
        ];
    }
}