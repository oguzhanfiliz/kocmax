<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductBackup extends Model
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

    // NO RELATIONSHIPS - TESTING ONLY
    
    // Basic scopes only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}