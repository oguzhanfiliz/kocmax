<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Services\CurrencyConversionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CurrencyMiddleware
{
    private CurrencyConversionService $currencyService;

    public function __construct(CurrencyConversionService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currency = $this->determineCurrency($request);
        
        // Set the currency in request for controllers
        $request->merge(['_currency' => $currency]);
        
        // Set global currency for the application
        App::instance('current.currency', $currency);
        
        return $next($request);
    }

    /**
     * Determine the currency from request
     */
    private function determineCurrency(Request $request): string
    {
        // 1. Check query parameter
        $currency = $request->query('currency');
        if ($currency && $this->isValidCurrency($currency)) {
            return strtoupper($currency);
        }

        // 2. Check header
        $currency = $request->header('X-Currency');
        if ($currency && $this->isValidCurrency($currency)) {
            return strtoupper($currency);
        }

        // 3. Check Accept-Language header for country-based currency
        $currency = $this->getCurrencyFromAcceptLanguage($request);
        if ($currency) {
            return $currency;
        }

        // 4. Check user preference (if authenticated)
        if ($request->user() && $request->user()->preferred_currency) {
            $currency = $request->user()->preferred_currency;
            if ($this->isValidCurrency($currency)) {
                return strtoupper($currency);
            }
        }

        // 5. Default currency
        return $this->getDefaultCurrency();
    }

    /**
     * Check if currency is valid and active
     */
    private function isValidCurrency(string $currency): bool
    {
        return Cache::remember("currency_valid_{$currency}", 3600, function () use ($currency) {
            return Currency::where('code', strtoupper($currency))
                ->where('is_active', true)
                ->exists();
        });
    }

    /**
     * Get currency from Accept-Language header
     */
    private function getCurrencyFromAcceptLanguage(Request $request): ?string
    {
        $locale = $request->getPreferredLanguage(['tr', 'en', 'de', 'fr', 'es']);
        
        $currencyMap = [
            'tr' => 'TRY',
            'en' => 'USD',
            'de' => 'EUR',
            'fr' => 'EUR',
            'es' => 'EUR',
        ];

        return $currencyMap[$locale] ?? null;
    }

    /**
     * Get default currency from database
     */
    private function getDefaultCurrency(): string
    {
        return Cache::remember('default_currency', 3600, function () {
            $defaultCurrency = Currency::default()->first();
            return $defaultCurrency ? $defaultCurrency->code : 'TRY';
        });
    }
}