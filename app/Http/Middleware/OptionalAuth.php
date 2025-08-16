<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Optional Authentication Middleware
 * 
 * Token varsa authenticate eder, yoksa guest olarak devam eder.
 * Smart pricing için kullanılır.
 */
class OptionalAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if Authorization header exists
        $token = $request->bearerToken();
        
        if ($token) {
            try {
                // Try to find and validate the token
                $accessToken = PersonalAccessToken::findToken($token);
                
                if ($accessToken && !$accessToken->tokenable_type) {
                    // Invalid token structure
                    return $next($request);
                }
                
                if ($accessToken && $accessToken->tokenable) {
                    // Valid token - authenticate user
                    Auth::setUser($accessToken->tokenable);
                    $request->setUserResolver(function () use ($accessToken) {
                        return $accessToken->tokenable;
                    });
                }
            } catch (\Exception $e) {
                // Token validation failed - continue as guest
                // Don't throw error, just log and continue
                logger('Optional auth failed: ' . $e->getMessage());
            }
        }
        
        // Continue with request (authenticated or guest)
        return $next($request);
    }
}
