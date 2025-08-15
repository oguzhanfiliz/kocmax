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
            return $this->addCorsHeaders($next($request), '*');
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
            // URL'den sadece domain kısmını çıkar (http:// https:// prefix'lerini temizle)
            $cleanDomain = parse_url($allowedDomain, PHP_URL_HOST) ?: $allowedDomain;
            
            // Exact match
            if ($domain === $cleanDomain) {
                return true;
            }
            
            // Port ile birlikte kontrol et
            if ($domain === $allowedDomain) {
                return true;
            }
            
            // Wildcard subdomain check (*.example.com)
            if (str_starts_with($cleanDomain, '*.')) {
                $baseDomain = substr($cleanDomain, 2);
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
        // Response object'ini direkt manipüle et, yeniden oluşturma
        if (method_exists($response, 'headers')) {
            // CORS headers'ını direkt ekle
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-API-Key');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');
        }

        return $response;
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