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
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Icon render helper
        Blade::directive('renderIcon', function ($expression) {
            return "<?php echo \App\Helpers\IconHelper::get($expression); ?>";
        });

        // Register observers
        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
        \App\Models\ProductVariant::observe(\App\Observers\ProductVariantObserver::class);
    }
}
