<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name', 
        'symbol',
        'exchange_rate',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:8',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($currency) {
            if ($currency->is_default) {
                self::where('is_default', true)->update(['is_default' => false]);
                $currency->exchange_rate = 1.0;
            }
        });

        static::updating(function ($currency) {
            if ($currency->is_default && $currency->isDirty('is_default')) {
                self::where('id', '!=', $currency->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
                $currency->exchange_rate = 1.0;
            }
        });
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function getDefault()
    {
        return self::default()->first();
    }

    public function convertTo($amount, Currency $targetCurrency)
    {
        if ($this->code === $targetCurrency->code) {
            return $amount;
        }

        // Convert to default currency first
        $defaultAmount = $amount / $this->exchange_rate;
        
        // Then convert to target currency
        return $defaultAmount * $targetCurrency->exchange_rate;
    }
}
