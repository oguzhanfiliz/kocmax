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
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories');
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
        $ids = collect([$this->id]);
        
        $this->children->each(function ($child) use (&$ids) {
            $ids = $ids->merge($child->getAllChildrenIds());
        });
        
        return $ids->unique()->values();
    }

    /**
     * Get hierarchical category name with indentation
     */
    public function getHierarchicalNameAttribute(): string
    {
        $prefix = str_repeat('→ ', $this->depth);
        return $prefix . $this->name;
    }

    /**
     * Get category depth level
     */
    public function getDepthAttribute(): int
    {
        $depth = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }
        
        return $depth;
    }

    /**
     * Get breadcrumb path
     */
    public function getBreadcrumbAttribute(): string
    {
        $breadcrumb = collect([$this->name]);
        $parent = $this->parent;
        
        while ($parent) {
            $breadcrumb->prepend($parent->name);
            $parent = $parent->parent;
        }
        
        return $breadcrumb->implode(' > ');
    }

    /**
     * Get categories for select with hierarchy
     */
    public static function getTreeForSelect(): array
    {
        $categories = self::with('children')->whereNull('parent_id')->ordered()->get();
        $result = [];
        
        foreach ($categories as $category) {
            self::buildTreeArray($category, $result, 0);
        }
        
        return $result;
    }

    /**
     * Build tree array recursively
     */
    private static function buildTreeArray($category, &$result, $depth)
    {
        $prefix = $depth > 0 ? str_repeat('  ', $depth) . '→ ' : '';
        $result[$category->id] = $prefix . $category->name;
        
        foreach ($category->children()->ordered()->get() as $child) {
            self::buildTreeArray($child, $result, $depth + 1);
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
