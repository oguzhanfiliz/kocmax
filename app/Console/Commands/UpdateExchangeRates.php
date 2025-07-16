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
        $this->info('Döviz kurları güncelleniyor...');
        $this->info('Aktif sağlayıcı: ' . $service->getProviderDisplayName());

        $result = $service->updateRates();

        if ($result['success']) {
            $this->info($result['message']);
            if (isset($result['currencies_updated'])) {
                $this->info("Güncellenen para birimi sayısı: {$result['currencies_updated']}");
            }
        } else {
            $this->error($result['message']);
            return 1;
        }

        return 0;
    }
}
