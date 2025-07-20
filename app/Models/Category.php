<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'parent_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->select(['id', 'name', 'slug', 'parent_id', 'sort_order', 'is_active']);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id')
            ->select(['products.id', 'products.name', 'products.slug', 'products.sku', 'products.base_price', 'products.is_active'])
            ->withPivot('id');
    }

    public function dealerDiscounts(): HasMany
    {
        return $this->hasMany(DealerDiscount::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getAllChildrenIds()
    {
        // Simplified version - only return direct children to prevent memory issues
        return \Cache::remember("category_children_{$this->id}", 3600, function () {
            return $this->children()->pluck('id');
        });
    }

    /**
     * Get hierarchical category name with indentation
     */
    public function getHierarchicalNameAttribute(): string
    {
        // Geçici olarak basitleştirildi
        return $this->name;
        // TODO: Fix depth calculation
        // $prefix = str_repeat('→ ', $this->depth);
        // return $prefix . $this->name;
    }

    /**
     * Get category depth level
     */
    public function getDepthAttribute(): int
    {
        // Geçici olarak devre dışı
        return 0;
        
        // TODO: Fix this
        // $depth = 0;
        // $parent = $this->parent;
        // $visited = [$this->id];
        // 
        // while ($parent && $depth < 10) { // Maksimum derinlik kontrolü
        //     if (in_array($parent->id, $visited)) {
        //         // Döngüsel referans tespit edildi
        //         break;
        //     }
        //     $visited[] = $parent->id;
        //     $depth++;
        //     $parent = $parent->parent;
        // }
        // 
        // return $depth;
    }

    /**
     * Get breadcrumb path
     */
    public function getBreadcrumbAttribute(): string
    {
        $breadcrumb = collect([$this->name]);
        $parent = $this->parent;
        $visited = [$this->id];
        $depth = 0;
        
        while ($parent && $depth < 10) {
            if (in_array($parent->id, $visited)) {
                // Döngüsel referans tespit edildi
                break;
            }
            $visited[] = $parent->id;
            $breadcrumb->prepend($parent->name);
            $parent = $parent->parent;
            $depth++;
        }
        
        return $breadcrumb->implode(' > ');
    }

    /**
     * Get categories for select with hierarchy
     */
    public static function getTreeForSelect(): array
    {
        return \Cache::remember('categories_tree_select', 1800, function () {
            // Sadece gerekli alanları seç ve bellek kullanımını azalt
            $allCategories = self::select(['id', 'name', 'parent_id', 'sort_order'])
                ->where('is_active', true)
                ->orderBy('parent_id', 'asc')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->limit(500) // Maksimum kategori sayısı
                ->get();
            
            // Kategorileri parent_id'ye göre grupla
            $categoriesByParent = $allCategories->groupBy('parent_id');
            
            $result = [];
            
            // Root kategorilerden başla
            if (isset($categoriesByParent[null])) {
                foreach ($categoriesByParent[null] as $category) {
                    self::buildTreeArrayOptimized($category, $result, 0, $categoriesByParent);
                }
            }
            
            return $result;
        });
    }

    /**
     * Build tree array recursively (optimized)
     */
    private static function buildTreeArrayOptimized($category, &$result, $depth, $categoriesByParent)
    {
        // Maksimum derinlik kontrolü - azaltıldı
        if ($depth > 5) {
            return;
        }
        
        $prefix = $depth > 0 ? str_repeat('  ', $depth) . '→ ' : '';
        $result[$category->id] = $prefix . $category->name;
        
        // Alt kategorileri kontrol et - maksimum 10 alt kategori
        if (isset($categoriesByParent[$category->id])) {
            $children = $categoriesByParent[$category->id];
            if ($children->count() > 10) {
                $children = $children->take(10);
            }
            foreach ($children as $child) {
                self::buildTreeArrayOptimized($child, $result, $depth + 1, $categoriesByParent);
            }
        }
    }

    /**
     * Get product attributes for this category
     */
    public function productAttributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class, 'category_attributes')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

}
