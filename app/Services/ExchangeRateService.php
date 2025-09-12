<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Döviz kuru güncelleme işlemlerini yöneten servis sınıfı.
 *
 * Uygulamanın döviz kuru sağlayıcısını konfigürasyona göre seçer ve
 * ilgili sağlayıcıdan (ör. TCMB) kurları günceller veya manuel kurları kullanır.
 */
class ExchangeRateService
{
    /**
     * TCMB üzerinden döviz kurlarıyla ilgili işlemleri yapan servis.
     *
     * @var TcmbExchangeRateService
     */
    private TcmbExchangeRateService $tcmbService;

    /**
     * Sınıfın bağımlılıklarını enjekte eder.
     *
     * @param TcmbExchangeRateService $tcmbService TCMB kuru sağlayıcısı servisi
     */
    public function __construct(TcmbExchangeRateService $tcmbService)
    {
        $this->tcmbService = $tcmbService;
    }

    /**
     * Konfigürasyonda belirtilen sağlayıcıya göre döviz kurlarını günceller.
     *
     * Sağlayıcı `tcmb` ise TCMB servisinden kurları çeker; aksi durumda manuel kurları kullanır.
     * Her durumda işlem sonucu ile ilgili bilgi içeren bir dizi döndürür.
     *
     * @return array{success:bool,message:string,currencies_updated?:int}
     */
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

    /**
     * Manuel döviz kuru kullanımını etkinleştirir.
     *
     * Varsayılan para biriminin kurunu 1.0 olarak günceller ve toplam para birimi
     * sayısını bilgilendirme için döndürür.
     *
     * @return array{success:bool,message:string,currencies_updated:int}
     * @throws Exception Varsayılan para birimi tanımlı değilse fırlatılır
     */
    private function useManualRates(): array
    {
        $defaultCurrency = Currency::getDefault();
        
        if (!$defaultCurrency) {
            throw new Exception('Varsayılan para birimi ayarlanmamış.');
        }

        // Varsayılan para biriminin kuru her zaman 1 olmalıdır
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

    /**
     * Sistemde desteklenen döviz kuru sağlayıcılarını döndürür.
     *
     * @return array<string,string> Sağlayıcı anahtarı => Görünen ad
     */
    public function getSupportedProviders(): array
    {
        return [
            'manual' => 'Manuel Kur Girişi',
            'tcmb' => 'TCMB (Otomatik)'
        ];
    }

    /**
     * Geçerli döviz kuru sağlayıcısının anahtarını döndürür.
     *
     * @return string Örn. "manual" veya "tcmb"
     */
    public function getCurrentProvider(): string
    {
        return config('services.exchange_rate.provider', 'manual');
    }

    /**
     * Geçerli sağlayıcının kullanıcıya gösterilecek adını döndürür.
     *
     * @return string Sağlayıcı adı; eşleşme yoksa "Bilinmiyor"
     */
    public function getProviderDisplayName(): string
    {
        $providers = $this->getSupportedProviders();
        $current = $this->getCurrentProvider();
        
        return $providers[$current] ?? 'Bilinmiyor';
    }
}
