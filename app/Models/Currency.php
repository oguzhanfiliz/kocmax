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

        // All exchange rates are relative to TRY (default currency)
        // If converting FROM TRY to another currency: divide by target rate
        // If converting TO TRY from another currency: multiply by source rate
        // If converting between two non-TRY currencies: first convert to TRY, then to target
        
        if ($this->is_default) {
            // FROM TRY to target currency
            return $amount / $targetCurrency->exchange_rate;
        } elseif ($targetCurrency->is_default) {
            // FROM source currency to TRY
            return $amount * $this->exchange_rate;
        } else {
            // FROM source currency to target currency (via TRY)
            $tryAmount = $amount * $this->exchange_rate;
            return $tryAmount / $targetCurrency->exchange_rate;
        }
    }
}
