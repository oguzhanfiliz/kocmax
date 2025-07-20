<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AdminMemoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Increase memory limit for admin panel
        if (request()->is('admin/*') || request()->is('admin')) {
            ini_set('memory_limit', '512M');
            
            // Set execution time
            set_time_limit(300); // 5 minutes
        }
    }
}
