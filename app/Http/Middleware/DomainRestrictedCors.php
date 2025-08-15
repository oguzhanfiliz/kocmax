<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DomainRestrictedCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Development'de domain koruması devre dışı
        if (!config('app.domain_protection_enabled', false)) {
            return $this->addCorsHeaders($next($request), '*');
        }

        $origin = $request->header('Origin');
        
        // Origin header yoksa (direct API call) devam et
        if (!$origin) {
            return $next($request);
        }

        $allowedDomains = config('cors.allowed_domains', []);
        $domain = parse_url($origin, PHP_URL_HOST);

        // Domain kontrolü
        if (!$this->isDomainAllowed($domain, $allowedDomains)) {
            return response()->json([
                'error' => 'Domain not allowed',
                'message' => 'Bu domain API erişimi için yetkilendirilmemiş.',
                'allowed_domains' => $allowedDomains
            ], 403);
        }

        return $this->addCorsHeaders($next($request), $origin);
    }

    /**
     * Domain'in izin verilen listede olup olmadığını kontrol et
     */
    private function isDomainAllowed(string $domain, array $allowedDomains): bool
    {
        foreach ($allowedDomains as $allowedDomain) {
            // Exact match
            if ($domain === $allowedDomain) {
                return true;
            }
            
            // Wildcard subdomain check (*.example.com)
            if (str_starts_with($allowedDomain, '*.')) {
                $baseDomain = substr($allowedDomain, 2);
                if (str_ends_with($domain, $baseDomain)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * CORS headers ekle
     */
    private function addCorsHeaders($response, string $origin)
    {
        if (!$response instanceof Response) {
            $response = response($response);
        }

        return $response->withHeaders([
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-API-Key',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400', // 24 hours
        ]);
    }

    /**
     * Preflight OPTIONS requests için
     */
    public function handlePreflight(Request $request): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $request->header('Origin', '*'))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-API-Key')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        return response('', 405);
    }
}