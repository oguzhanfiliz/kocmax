<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StorageCorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Sadece storage dosyaları için CORS header'ları ekle
        if (str_contains($request->getPathInfo(), '/storage/')) {
            $origin = $request->header('Origin');
            $allowedOrigins = [
                'http://localhost:3000',
                'http://localhost:5173', 
                'http://127.0.0.1:3000',
                'http://localhost:8003',
                'http://127.0.0.1:8003'
            ];
            
            if ($origin && in_array($origin, $allowedOrigins)) {
                $response->header('Access-Control-Allow-Origin', $origin);
                $response->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
                $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization');
                $response->header('Access-Control-Max-Age', '3600');
                $response->header('Cache-Control', 'public, max-age=3600');
            }
        }
        
        return $response;
    }
}
