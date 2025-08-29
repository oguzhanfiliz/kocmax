<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ThrottleDealerApplication
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Localhost ve development için rate limiting'i devre dışı bırak
        if ($request->header('Origin') === 'http://localhost:3000' || 
            $request->header('Origin') === 'https://localhost:3000' ||
            $request->ip() === '127.0.0.1' ||
            $request->ip() === '::1' ||
            $request->ip() === 'localhost' ||
            str_contains($request->ip(), '192.168.') ||
            str_contains($request->ip(), '10.') ||
            app()->environment() === 'local' ||
            app()->environment() === 'development') {
            
            Log::info('Rate limiting devre dışı - localhost/development', [
                'ip' => $request->ip(),
                'origin' => $request->header('Origin'),
                'environment' => app()->environment()
            ]);
            
            return $next($request);
        }
        
        $key = $this->resolveRequestSignature($request);
        
        $maxAttempts = 3; // 3 başvuru
        $decayMinutes = 60; // 1 saat içinde
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Çok fazla bayi başvurusu yaptınız. ' . ceil($seconds / 60) . ' dakika sonra tekrar deneyin.',
                'retry_after' => $seconds,
            ], 429);
        }
        
        $response = $next($request);
        
        // Sadece POST istekleri için rate limiting uygula
        if ($request->isMethod('post')) {
            RateLimiter::hit($key, $decayMinutes * 60);
        }
        
        return $response;
    }
    
    /**
     * Resolve request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        // IP adresi ve kullanıcı ID'si kombinasyonu
        $userId = auth()->id() ?? 'guest';
        return sha1($request->ip() . '|' . $userId . '|dealer_application');
    }
}
