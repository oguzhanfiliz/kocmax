<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VariantType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'input_type',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    const INPUT_TYPES = [
        'select' => 'Dropdown',
        'radio' => 'Radio Buttons',
        'color' => 'Color Picker',
        'image' => 'Image Selector',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($type) {
            if (empty($type->slug)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    /**
     * Type has many options
     */
    public function options(): HasMany
    {
        return $this->hasMany(VariantOption::class);
    }

    /**
     * Active types scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Required types scope
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Ordered types scope
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
