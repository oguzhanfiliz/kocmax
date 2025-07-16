<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    protected $signature = 'app:update-exchange-rates';
    protected $description = 'Update exchange rates from an external API';

    public function handle(ExchangeRateService $exchangeRateService)
    {
        $this->info('Updating exchange rates...');

        try {
            $exchangeRateService->updateExchangeRates();
            $this->info('Exchange rates updated successfully.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
