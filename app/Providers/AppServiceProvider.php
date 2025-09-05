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

        // Register Setting Service
        $this->app->singleton(\App\Services\SettingService::class);

        // Register Pricing System Services
        $this->registerPricingServices();
        
        // Register Cart System Services
        $this->registerCartServices();
        
        // Register Payment System Services
        $this->registerPaymentServices();
        
        // Register Image Optimization Service
        $this->app->singleton(\App\Services\ImageOptimizationService::class);

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
     * Register cart system services
     */
    private function registerCartServices(): void
    {
        // Register Cart Strategy Factory
        $this->app->singleton(\App\Services\Cart\CartStrategyFactory::class);
        
        // Register Cart Strategies
        $this->app->singleton(\App\Services\Cart\AuthenticatedCartStrategy::class);
        $this->app->singleton(\App\Services\Cart\GuestCartStrategy::class);
        
        // Bind CartStrategyInterface to default implementation
        // Note: This is a dynamic binding - actual strategy is determined by CartStrategyFactory
        $this->app->bind(
            \App\Contracts\Cart\CartStrategyInterface::class,
            function ($app) {
                // Default to authenticated strategy - this will be overridden by CartService
                return $app->make(\App\Services\Cart\AuthenticatedCartStrategy::class);
            }
        );
        
        // Register CartService Interface
        $this->app->bind(
            \App\Contracts\Cart\CartServiceInterface::class,
            \App\Services\Cart\CartService::class
        );
        
        // Register CartService as singleton
        $this->app->singleton(\App\Services\Cart\CartService::class);
    }

    /**
     * Register payment system services
     */
    private function registerPaymentServices(): void
    {
        // Register PaymentManager as singleton
        $this->app->singleton(\App\Services\Payment\PaymentManager::class, function ($app) {
            $paymentManager = new \App\Services\Payment\PaymentManager();
            
            // Register payment strategies
            $paymentManager->register('paytr', new \App\Services\Payment\Strategies\PayTrPaymentStrategy($app->environment() !== 'production'));
            
            return $paymentManager;
        });
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
        
        // ProductCertificate Observer
        \App\Models\ProductCertificate::observe(\App\Observers\ProductCertificateObserver::class);
        
        // ProductImage Observer (Resim optimizasyonu için)
        \App\Models\ProductImage::observe(\App\Observers\ProductImageObserver::class);
    }
}
