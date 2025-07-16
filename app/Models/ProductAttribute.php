<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'attribute_type_id',
        'options',
        'is_required',
        'is_variant',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_variant' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            if (empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });
    }

    /**
     * Get the attribute type
     */
    public function attributeType(): BelongsTo
    {
        return $this->belongsTo(AttributeType::class);
    }

    /**
     * Get categories that have this attribute
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_attributes')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Get all product values for this attribute
     */
    public function productValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Get all variant values for this attribute
     */
    public function variantValues(): HasMany
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }

    /**
     * Scope to get only variant attributes
     */
    public function scopeVariants($query)
    {
        return $query->where('is_variant', true);
    }

    /**
     * Scope to get only active attributes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted options for select/checkbox/radio
     */
    public function getFormattedOptionsAttribute(): array
    {
        if (!$this->options) {
            return [];
        }

        return collect($this->options)->mapWithKeys(function ($option) {
            return [$option['value'] => $option['label']];
        })->toArray();
    }
}
