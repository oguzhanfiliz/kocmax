<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulkDiscount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'min_quantity',
        'discount_percentage',
        'is_active',
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForQuantity($query, $quantity)
    {
        return $query->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc');
    }

    public static function getApplicableDiscount($productId, $quantity)
    {
        return self::active()
            ->forProduct($productId)
            ->forQuantity($quantity)
            ->first();
    }

    public function calculateDiscountAmount($price)
    {
        return $price * ($this->discount_percentage / 100);
    }
}
