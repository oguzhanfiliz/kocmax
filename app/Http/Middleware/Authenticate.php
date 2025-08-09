<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * For API-only application, we don't redirect but return null for JSON responses.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, return null (will trigger 401 Unauthorized response)
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // For web requests, redirect to a simple login info page
        return '/login-required';
    }
}
