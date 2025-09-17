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
        // Product model binding - supports both ID and slug, excludes soft deleted and inactive
        Route::bind('product', function ($value) {
            return \App\Models\Product::where(function ($query) use ($value) {
                $query->where('id', $value)
                      ->orWhere('slug', $value);
            })
            ->where('is_active', true) // Sadece aktif ürünleri getir
            ->whereNull('deleted_at') // Soft delete edilmiş ürünleri hariç tut
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

        // Contact form rate limiting - spam koruması için katı limit
        RateLimiter::for('contact', function (Request $request) {
            $limit = app()->environment('local') ? 100 : 5; // Development vs Production
            return Limit::perMinute($limit)->by($request->ip())->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Çok fazla mesaj gönderiyorsunuz. Lütfen bir dakika bekleyin.',
                    'retry_after' => 60
                ], 429);
            });
        });

        // Authenticated users için çok yüksek limit - Development'ta neredeyse unlimited
        RateLimiter::for('authenticated', function (Request $request) {
            $limit = app()->environment('local') ? 50000 : 1000; // Development vs Production
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        // Dealer application rate limiting - çok katı limit
        RateLimiter::for('dealer-applications', function (Request $request) {
            $limit = app()->environment('local') ? 100 : 3; // Development vs Production
            $key = $request->user()?->id ?: $request->ip();
            
            return [
                Limit::perHour($limit)->by($key)->response(function () {
                    return response()->json([
                        'error' => 'Too many dealer applications',
                        'message' => 'Çok fazla bayi başvurusu yaptınız. Lütfen 1 saat bekleyin.',
                        'retry_after' => 3600
                    ], 429);
                }),
                // Günlük limit de ekle
                Limit::perDay(app()->environment('local') ? 500 : 5)->by($key)
            ];
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
