<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use ErrorException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Laravel Ignition Livewire foreach hatası için özel handling
            if ($e instanceof ErrorException && 
                str_contains($e->getMessage(), 'foreach() argument must be of type array|object, null given') &&
                str_contains($e->getFile(), 'LaravelLivewireRequestContextProvider.php')) {
                
                // Bu hatayı log'a yazmayı durdur
                return false;
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Laravel Ignition Livewire foreach hatası için özel handling
        if ($e instanceof ErrorException && 
            str_contains($e->getMessage(), 'foreach() argument must be of type array|object, null given') &&
            str_contains($e->getFile(), 'LaravelLivewireRequestContextProvider.php')) {
            
            // Hatayı logla ama kullanıcıya farklı bir response ver
            \Log::warning('Ignition Livewire context provider hatası yakalandı ve bypass edildi.');
            
            // Normal Laravel hata sayfasını göster
            if (config('app.debug')) {
                return response()->view('errors.500', [
                    'message' => 'Geçici bir sistem hatası oluştu. Sayfayı yenilemeyi deneyin.'
                ], 500);
            }
            
            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // For API requests, return JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login to access this resource.',
                'error_code' => 'AUTHENTICATION_REQUIRED',
                'login_endpoint' => '/api/v1/auth/login'
            ], 401);
        }

        // Filament Admin paneli için özel yönlendirme
        // Admin panel path: /admin, login route adı: filament.admin.auth.login
        if ($request->routeIs('filament.admin.*') || $request->is('admin') || $request->is('admin/*')) {
            // intended URL korunarak Filament login sayfasına yönlendir
            return redirect()->guest(route('filament.admin.auth.login'));
        }

        // For web requests, redirect to login info page
        return redirect()->to('/login-required');
    }
}
