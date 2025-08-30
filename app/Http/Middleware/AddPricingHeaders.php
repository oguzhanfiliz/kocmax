<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddPricingHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Development modda sadece temel headers
        if ($user = $request->user()) {
            $customerType = $this->determineCustomerType($user);
            $response->headers->set('X-Customer-Type', $customerType);
            $response->headers->set('X-Is-Dealer', in_array($customerType, ['B2B', 'WHOLESALE']) ? 'true' : 'false');
        } else {
            $response->headers->set('X-Customer-Type', 'B2C');
            $response->headers->set('X-Is-Dealer', 'false');
        }
        
        return $response;
    }
    
    private function determineCustomerType($user): string
    {
        // Customer type override varsa öncelik ver
        if (!empty($user->customer_type_override)) {
            return strtoupper($user->customer_type_override);
        }
        
        // Roller kontrol et
        if ($user->hasRole('wholesale')) {
            return 'WHOLESALE';
        }
        
        if ($user->hasRole('dealer') || $user->is_approved_dealer) {
            return 'B2B';
        }
        
        if ($user->hasRole('retail')) {
            return 'RETAIL';
        }
        
        // Company bilgisi varsa B2B kabul et
        if (!empty($user->company_name) || !empty($user->tax_number)) {
            return 'B2B';
        }
        
        // Lifetime value yüksekse wholesale
        if (($user->lifetime_value ?? 0) >= 50000) {
            return 'WHOLESALE';
        }
        
        // Default B2C
        return 'B2C';
    }
}
