<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductClean extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'barcode',
        'base_price',
        'weight',
        'is_active',
        'is_featured',
        'is_new',
        'is_bestseller',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'sort_order',
        'brand',
        'model',
        'material',
        'gender',
        'safety_standard',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_bestseller' => 'boolean',
        'sort_order' => 'integer',
    ];

    // TEST: Add back boot method
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
    }

    private static function generateSku($product)
    {
        $prefix = 'PRD';
        $timestamp = now()->format('ymd');
        $random = strtoupper(Str::random(3));
        
        return "{$prefix}-{$timestamp}-{$random}";
    }

    // TEST: Add back one relationship at a time
    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id')
            ->select(['categories.id', 'categories.name', 'categories.slug'])
            ->withPivot('id')
            ->withoutGlobalScopes();
    }

    // NO ACCESSORS

    public function getRouteKeyName()
    {
        return 'slug';
    }
}