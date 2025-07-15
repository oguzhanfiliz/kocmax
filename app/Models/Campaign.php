<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'discount_type',
        'discount_value',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'campaign_products');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function isActive()
    {
        return $this->is_active &&
               $this->start_date->isPast() &&
               $this->end_date->isFuture();
    }

    public function isUpcoming()
    {
        return $this->is_active && $this->start_date->isFuture();
    }

    public function isExpired()
    {
        return $this->end_date->isPast();
    }

    public function calculateDiscount($price)
    {
        if (!$this->isActive()) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $price * ($this->discount_value / 100);
        }

        return min($this->discount_value, $price);
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->end_date);
    }
}
