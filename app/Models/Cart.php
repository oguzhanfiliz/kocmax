<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'total_amount',
        'discounted_amount',
        'coupon_code',
        'coupon_discount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discounted_amount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(DiscountCoupon::class, 'coupon_code', 'code');
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->current_price;
        });

        $this->total_amount = $subtotal;
        
        // Apply coupon discount if exists
        if ($this->coupon_code && $this->coupon) {
            $discount = 0;
            
            if ($this->coupon->type === 'percentage') {
                $discount = $subtotal * ($this->coupon->value / 100);
            } else {
                $discount = min($this->coupon->value, $subtotal);
            }
            
            $this->coupon_discount = $discount;
            $this->discounted_amount = $subtotal - $discount;
        } else {
            $this->coupon_discount = 0;
            $this->discounted_amount = $subtotal;
        }
        
        $this->save();
    }

    public function clear()
    {
        $this->items()->delete();
        $this->update([
            'total_amount' => 0,
            'discounted_amount' => 0,
            'coupon_code' => null,
            'coupon_discount' => 0,
        ]);
    }

    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }
}
