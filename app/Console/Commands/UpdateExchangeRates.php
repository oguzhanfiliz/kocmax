<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    protected $signature = 'app:update-rates';
    protected $description = 'Update currency rates from TCMB or keep manual rates';

    public function handle(ExchangeRateService $service)
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        
        $this->info("[$timestamp] Döviz kurları güncelleniyor...");
        $this->info("[$timestamp] Aktif sağlayıcı: " . $service->getProviderDisplayName());

        $result = $service->updateRates();

        if ($result['success']) {
            $this->info("[$timestamp] " . $result['message']);
            if (isset($result['currencies_updated'])) {
                $this->info("[$timestamp] Güncellenen para birimi sayısı: {$result['currencies_updated']}");
            }
        } else {
            $this->error("[$timestamp] " . $result['message']);
            return 1;
        }

        return 0;
    }
}
