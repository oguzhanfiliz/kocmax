<?php

namespace App\Helpers;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;

class CurrencyHelper
{
    /**
     * Get active currencies for form options with flags
     */
    public static function getActiveCurrencyOptions(): array
    {
        return Cache::remember('active_currencies_options', 300, function () {
            return Currency::where('is_active', true)
                ->orderBy('is_default', 'desc')
                ->orderBy('name')
                ->get()
                ->pluck('name', 'code')
                ->toArray();
        });
    }

    /**
     * Get currency symbol by code
     */
    public static function getCurrencySymbol(string $currencyCode): string
    {
        $currency = Cache::remember("currency_symbol_{$currencyCode}", 300, function () use ($currencyCode) {
            return Currency::where('code', $currencyCode)->first();
        });
        
        return $currency ? $currency->symbol : $currencyCode;
    }

    /**
     * Get currency name by code
     */
    public static function getCurrencyName(string $currencyCode): string
    {
        $currency = Cache::remember("currency_name_{$currencyCode}", 300, function () use ($currencyCode) {
            return Currency::where('code', $currencyCode)->first();
        });
        
        return $currency ? $currency->name : $currencyCode;
    }

    /**
     * Clear currency cache
     */
    public static function clearCache(): void
    {
        Cache::forget('active_currencies_options');
        // Also clear individual currency caches
        Currency::all()->each(function ($currency) {
            Cache::forget("currency_symbol_{$currency->code}");
            Cache::forget("currency_name_{$currency->code}");
        });
    }
}