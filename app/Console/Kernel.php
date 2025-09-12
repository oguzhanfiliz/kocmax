<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Döviz kuru güncelleme cron'u
        if (config('services.exchange_rate.provider') === 'tcmb') {
            $schedule->command('app:update-rates')
                ->hourly() // Her saat başı TCMB'den güncelle
                ->withoutOverlapping()
                ->onOneServer()
                ->runInBackground()
                ->sendOutputTo(storage_path('logs/exchange_rate_cron.log'));
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
