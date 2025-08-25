<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Product model binding - supports both ID and slug
        Route::bind('product', function ($value) {
            return \App\Models\Product::where('id', $value)
                ->orWhere('slug', $value)
                ->firstOrFail();
        });

        // Dynamic rate limiting based on environment - Development'ta çok yüksek limitler
        RateLimiter::for('api', function (Request $request) {
            $limit = app()->environment('local') ? 10000 : 100; // Development vs Production
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        // Authentication rate limiting - development'ta yüksek limit
        RateLimiter::for('auth', function (Request $request) {
            $limit = app()->environment('local') ? 1000 : 50; // Development vs Production  
            return Limit::perMinute($limit)->by($request->ip());
        });

        // Checkout rate limiting - development'ta unlimited benzeri
        RateLimiter::for('checkout', function (Request $request) {
            $limit = app()->environment('local') ? 5000 : 10; // Development vs Production
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        // Campaign rate limiting - development'ta yüksek limit
        RateLimiter::for('campaigns', function (Request $request) {
            $limit = app()->environment('local') ? 2000 : 30; // Development vs Production
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        // Public API rate limiting (ürünler, kategoriler için) - Development'ta çok yüksek
        RateLimiter::for('public', function (Request $request) {
            $limit = app()->environment('local') ? 15000 : 200; // Development vs Production
            return Limit::perMinute($limit)->by($request->ip())->response(function () {
                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Çok fazla istek gönderiyorsunuz. Lütfen bir dakika bekleyin.',
                    'retry_after' => 60
                ], 429);
            });
        });

        // Authenticated users için çok yüksek limit - Development'ta neredeyse unlimited
        RateLimiter::for('authenticated', function (Request $request) {
            $limit = app()->environment('local') ? 50000 : 1000; // Development vs Production
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
