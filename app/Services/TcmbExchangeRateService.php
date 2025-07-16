<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;
use Exception;

class TcmbExchangeRateService
{
    private const TCMB_API_URL = 'https://www.tcmb.gov.tr/kurlar/today.xml';
    private const CACHE_KEY = 'tcmb_rates';
    private const CACHE_DURATION = 3600; // 1 hour
    private const REQUEST_TIMEOUT = 30;
    
    private array $supportedCurrencies = [
        'USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'SEK', 'NOK', 'DKK'
    ];

    public function updateRates(): array
    {
        try {
            $xmlData = $this->fetchXmlData();
            $rates = $this->parseXmlData($xmlData);
            $this->updateCurrencyRates($rates);
            
            Log::info('TCMB döviz kurları başarıyla güncellendi', [
                'currencies_updated' => count($rates),
                'timestamp' => now()
            ]);
            
            return [
                'success' => true,
                'message' => 'Döviz kurları başarıyla güncellendi',
                'currencies_updated' => count($rates)
            ];
            
        } catch (Exception $e) {
            Log::error('TCMB döviz kurları güncellenemedi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Döviz kurları güncellenemedi: ' . $e->getMessage()
            ];
        }
    }

    private function fetchXmlData(): string
    {
        $cacheKey = self::CACHE_KEY . '_xml';
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withHeaders([
                    'User-Agent' => 'Laravel-App-Rate-Service',
                    'Accept' => 'application/xml, text/xml',
                ])
                ->get(self::TCMB_API_URL);

            if ($response->failed()) {
                throw new Exception(
                    'TCMB API\'den veri alınamadı. Durum: ' . $response->status()
                );
            }

            $xmlContent = $response->body();
            
            if (empty($xmlContent)) {
                throw new Exception('TCMB API\'den boş yanıt alındı');
            }

            $this->validateXmlContent($xmlContent);
            
            return $xmlContent;
        });
    }

    private function validateXmlContent(string $xmlContent): void
    {
        try {
            $xml = new SimpleXMLElement($xmlContent);

            if (!isset($xml->Currency) || count($xml->Currency) === 0) {
                throw new Exception('XML\'de para birimi verisi bulunamadı');
            }
        } catch (Exception $e) {
            throw new Exception('TCMB\'den geçersiz XML formatı alındı: ' . $e->getMessage());
        }
    }

    private function parseXmlData(string $xmlData): array
    {
        libxml_use_internal_errors(true);
        
        try {
            $xml = new SimpleXMLElement($xmlData);
        } catch (Exception $e) {
            throw new Exception('XML verisi ayrıştırılamadı: ' . $e->getMessage());
        }

        $rates = [];
        
        foreach ($xml->Currency as $currency) {
            $code = (string) $currency['CurrencyCode'];
            
            if (!in_array($code, $this->supportedCurrencies)) {
                continue;
            }

            $buyingRate = $this->parseRate((string) $currency->BanknoteBuying);
            $sellingRate = $this->parseRate((string) $currency->BanknoteSelling);
            
            if ($buyingRate === null || $sellingRate === null) {
                Log::warning('Geçersiz kur verisi', ['currency' => $code]);
                continue;
            }

            $rates[$code] = [
                'buying_rate' => $buyingRate,
                'selling_rate' => $sellingRate,
                'cross_rate' => $this->parseRate((string) $currency->CrossRateUSD),
                'unit' => (int) $currency->Unit
            ];
        }

        if (empty($rates)) {
            throw new Exception('XML\'de geçerli döviz kuru bulunamadı');
        }

        return $rates;
    }

    private function parseRate(string $rate): ?float
    {
        if (empty($rate) || $rate === '0') {
            return null;
        }

        $cleanRate = str_replace(',', '.', $rate);
        
        if (!is_numeric($cleanRate)) {
            return null;
        }

        return (float) $cleanRate;
    }

    private function updateCurrencyRates(array $rates): void
    {
        $defaultCurrency = Currency::getDefault();
        
        if (!$defaultCurrency) {
            throw new Exception('Varsayılan para birimi ayarlanmamış');
        }

        // TCMB sağlayıcısı TRY'ı temel alır
        // Sistemin varsayılan para birimi TRY değilse işlem yapılamaz
        if ($defaultCurrency->code !== 'TRY') {
            throw new Exception('TCMB sağlayıcısı için varsayılan para birimi TRY olmalıdır.');
        }

        foreach ($rates as $currencyCode => $rateData) {
            $currency = Currency::where('code', $currencyCode)->first();
            
            if (!$currency) {
                Log::warning('TCMB\'deki para birimi veritabanında bulunamadı, güncelleme atlanıyor.', ['code' => $currencyCode]);
                continue;
            }

            $rate = $this->calculateRate($rateData, $defaultCurrency->code);
            
            if ($rate > 0) {
                $currency->update(['exchange_rate' => $rate]);
            }
        }

        // Varsayılan para biriminin (TRY) kuru her zaman 1 olmalı
        $defaultCurrency->update(['exchange_rate' => 1.0]);
    }

    private function calculateRate(array $rateData, string $baseCurrency): float
    {
        $rate = $rateData['buying_rate'] ?? $rateData['selling_rate'] ?? 0;
        $unit = $rateData['unit'] ?? 1;

        if ($rate <= 0 || $unit <= 0) {
            return 0;
        }

        return $rate / $unit;
    }

    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    public function isCurrencySupported(string $currencyCode): bool
    {
        return in_array(strtoupper($currencyCode), $this->supportedCurrencies);
    }

    public function getCachedRates(): ?array
    {
        return Cache::get(self::CACHE_KEY);
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '_xml');
    }
}