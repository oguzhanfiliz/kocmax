<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VariantOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_type_id',
        'name',
        'value',
        'slug',
        'hex_color',
        'image_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Option belongs to a variant type
     */
    public function variantType(): BelongsTo
    {
        return $this->belongsTo(VariantType::class);
    }

    /**
     * Option belongs to many product variants
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_options');
    }

    /**
     * Active options scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Ordered options scope
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get display value
     */
    public function getDisplayValueAttribute()
    {
        return $this->value ?: $this->name;
    }
}
