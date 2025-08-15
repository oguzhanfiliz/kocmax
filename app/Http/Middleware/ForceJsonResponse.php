<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Force JSON response for API routes
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force Accept header to application/json for API routes
        $request->headers->set('Accept', 'application/json');
        
        // Process request
        $response = $next($request);
        
        // Ensure response Content-Type is JSON and content is JSON
        if (method_exists($response, 'headers')) {
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        }
        
        return $response;
    }
}