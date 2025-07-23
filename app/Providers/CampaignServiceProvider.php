<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Campaign\CampaignEngine;
use App\Services\Campaign\Handlers\BundleDiscountHandler;
use App\Services\Campaign\Handlers\BuyXGetYFreeHandler;
use App\Services\Campaign\Handlers\FlashSaleHandler;
use App\Services\Campaign\Handlers\FreeShippingHandler;
use Illuminate\Support\ServiceProvider;

class CampaignServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CampaignEngine::class, function ($app) {
            $engine = new CampaignEngine(
                cachingEnabled: config('campaign.caching_enabled', true),
                cacheLifetime: config('campaign.cache_lifetime', 3600)
            );

            // Register campaign handlers
            $engine->registerHandler(new BuyXGetYFreeHandler());
            $engine->registerHandler(new BundleDiscountHandler());
            $engine->registerHandler(new FlashSaleHandler());
            $engine->registerHandler(new FreeShippingHandler());

            return $engine;
        });
    }

    public function boot(): void
    {
        // Publish campaign configuration if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/campaign.php' => config_path('campaign.php'),
            ], 'campaign-config');
        }
    }
}