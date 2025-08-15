<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        
        // Check if IP is blacklisted
        if ($this->isBlacklisted($ip)) {
            Log::warning('Blacklisted IP attempted API access', ['ip' => $ip]);
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // Detect suspicious activity patterns
        if ($this->detectSuspiciousActivity($request)) {
            Log::warning('Suspicious API activity detected', [
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            
            // Temporarily blacklist aggressive IPs
            $this->temporaryBlacklist($ip);
        }

        // Add security headers
        $response = $next($request);
        
        if (method_exists($response, 'headers')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        return $response;
    }

    /**
     * Check if IP is blacklisted
     */
    private function isBlacklisted(string $ip): bool
    {
        // Check permanent blacklist (could be stored in database)
        $permanentBlacklist = config('security.blacklisted_ips', []);
        
        if (in_array($ip, $permanentBlacklist)) {
            return true;
        }

        // Check temporary blacklist (stored in cache)
        return Cache::has("blacklist:$ip");
    }

    /**
     * Detect suspicious activity patterns
     */
    private function detectSuspiciousActivity(Request $request): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Skip security checks in development environment
        if (app()->environment('local') || config('app.debug')) {
            return false;
        }
        
        // Track request frequency per IP
        $requestKey = "api_requests:$ip";
        $requestCount = Cache::get($requestKey, 0);
        Cache::put($requestKey, $requestCount + 1, 300); // 5 minutes
        
        // Configurable limits based on environment
        $requestLimit = config('security.api_request_limit', 500); // Increased default limit
        
        // Suspicious patterns
        $suspiciousPatterns = [
            // Too many requests in short time (more than rate limiter allows)
            $requestCount > $requestLimit,
            
            // Suspicious user agents
            empty($userAgent),
            str_contains(strtolower($userAgent ?? ''), 'bot') && 
            !str_contains(strtolower($userAgent ?? ''), 'googlebot'),
            
            // Common attack patterns in URL
            str_contains($request->fullUrl(), '..'),
            str_contains($request->fullUrl(), '<script'),
            str_contains($request->fullUrl(), 'union select'),
            
            // Suspicious request patterns
            $request->hasHeader('X-Forwarded-For') && 
            count(explode(',', $request->header('X-Forwarded-For'))) > 3,
        ];

        return in_array(true, $suspiciousPatterns, true);
    }

    /**
     * Temporarily blacklist an IP
     */
    private function temporaryBlacklist(string $ip): void
    {
        // Blacklist for 1 hour
        Cache::put("blacklist:$ip", true, 3600);
        
        Log::warning('IP temporarily blacklisted for suspicious activity', ['ip' => $ip]);
    }
}
