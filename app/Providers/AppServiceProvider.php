<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Spatie\Ignition\Ignition;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('App\Helpers\IconHelper', function () {
            return new \App\Helpers\IconHelper();
        });

        // Register Pricing System Services
        $this->registerPricingServices();

        // Laravel Ignition Livewire Context Provider hatası için geçici çözüm
        if (class_exists(Ignition::class)) {
            $this->app->singleton('flare.context_providers', function ($app) {
                return collect(config('flare.context_providers', []))
                    ->reject(function ($provider) {
                        return str_contains($provider, 'LaravelLivewireRequestContextProvider');
                    })
                    ->values()
                    ->all();
            });
        }
    }

    /**
     * Register pricing system services
     */
    private function registerPricingServices(): void
    {
        // Register CustomerTypeDetector
        $this->app->singleton(\App\Services\Pricing\CustomerTypeDetector::class);
        
        // Register PriceEngine with all strategies
        $this->app->singleton(\App\Services\Pricing\PriceEngine::class, function ($app) {
            $customerTypeDetector = $app->make(\App\Services\Pricing\CustomerTypeDetector::class);
            $priceEngine = new \App\Services\Pricing\PriceEngine($customerTypeDetector);
            
            // Register all pricing strategies
            $priceEngine->addStrategy(new \App\Services\Pricing\B2BPricingStrategy());
            $priceEngine->addStrategy(new \App\Services\Pricing\B2CPricingStrategy());
            $priceEngine->addStrategy(new \App\Services\Pricing\GuestPricingStrategy());
            
            return $priceEngine;
        });
        
        // Bind PricingService interface to concrete implementation
        $this->app->bind(
            \App\Interfaces\Pricing\PricingServiceInterface::class,
            \App\Services\PricingService::class
        );
        
        // Register PricingService as singleton
        $this->app->singleton(\App\Services\PricingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Icon render helper
        Blade::directive('renderIcon', function ($expression) {
            return "<?php echo \App\Helpers\IconHelper::get($expression); ?>";
        });

        // Observer'lar geçici olarak devre dışı - bellek sorunu çözümü için
        // \App\Models\Product::observe(\App\Observers\ProductObserver::class);
        // \App\Models\ProductVariant::observe(\App\Observers\ProductVariantObserver::class);
    }
}
