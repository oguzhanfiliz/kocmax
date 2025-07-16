<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Log;
use Exception;

class ExchangeRateService
{
    private TcmbExchangeRateService $tcmbService;

    public function __construct(TcmbExchangeRateService $tcmbService)
    {
        $this->tcmbService = $tcmbService;
    }

    public function updateRates(): array
    {
        $provider = config('services.exchange_rate.provider', 'manual');
        
        try {
            switch ($provider) {
                case 'tcmb':
                    return $this->tcmbService->updateRates();
                    
                case 'manual':
                default:
                    return $this->useManualRates();
            }
        } catch (Exception $e) {
            Log::error('Döviz kuru güncellemesi başarısız', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Döviz kurları güncellenemedi: ' . $e->getMessage()
            ];
        }
    }

    private function useManualRates(): array
    {
        $defaultCurrency = Currency::getDefault();
        
        if (!$defaultCurrency) {
            throw new Exception('Varsayılan para birimi ayarlanmamış.');
        }

        // Varsayılan para biriminin kuru her zaman 1 olmalı
        $defaultCurrency->update(['exchange_rate' => 1.0]);

        $totalCurrencies = Currency::count();

        Log::info('Manuel döviz kurları kullanılıyor', [
            'default_currency' => $defaultCurrency->code,
            'total_currencies' => $totalCurrencies
        ]);

        return [
            'success' => true,
            'message' => 'Manuel döviz kurları kullanılıyor',
            'currencies_updated' => $totalCurrencies
        ];
    }

    public function getSupportedProviders(): array
    {
        return [
            'manual' => 'Manuel Kur Girişi',
            'tcmb' => 'TCMB (Otomatik)'
        ];
    }

    public function getCurrentProvider(): string
    {
        return config('services.exchange_rate.provider', 'manual');
    }

    public function getProviderDisplayName(): string
    {
        $providers = $this->getSupportedProviders();
        $current = $this->getCurrentProvider();
        
        return $providers[$current] ?? 'Bilinmiyor';
    }
}
