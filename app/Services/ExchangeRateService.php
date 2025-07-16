<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function updateExchangeRates()
    {
        $defaultCurrency = Currency::getDefault();
        if (!$defaultCurrency) {
            throw new \Exception('No default currency set.');
        }

        $apiKey = config('services.exchangerate-api.key');
        if (!$apiKey) {
            throw new \Exception('ExchangeRate-API key not set.');
        }

        $response = Http::get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$defaultCurrency->code}");

        if ($response->failed()) {
            throw new \Exception('Failed to fetch exchange rates from API.');
        }

        $rates = $response->json()['conversion_rates'];

        $currencies = Currency::where('code', '!=', $defaultCurrency->code)->get();

        foreach ($currencies as $currency) {
            if (isset($rates[$currency->code])) {
                $currency->update(['exchange_rate' => $rates[$currency->code]]);
            }
        }
    }
}
