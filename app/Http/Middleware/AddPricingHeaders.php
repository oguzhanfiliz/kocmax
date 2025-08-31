<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Pricing\CustomerTypeDetectorService;

class AddPricingHeaders
{
    private CustomerTypeDetectorService $customerTypeDetector;

    public function __construct(CustomerTypeDetectorService $customerTypeDetector)
    {
        $this->customerTypeDetector = $customerTypeDetector;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // 🔒 Güvenli Token-Based Pricing: Sadece token'dan gelen user bilgilerine güven
        if ($user = $request->user()) {
            // Token'dan gelen user bilgilerine göre customer type belirle
            $customerType = $this->customerTypeDetector->getCustomerType($user);
            $isDealer = $this->customerTypeDetector->isDealer($user);
            
            // Response header'larına ekle
            $response->headers->set('X-Customer-Type', strtoupper($customerType));
            $response->headers->set('X-Is-Dealer', $isDealer ? 'true' : 'false');
            
            // 🔒 Token manipulation koruması için hash ekle
            $pricingToken = $this->generatePricingToken($user, $customerType);
            $response->headers->set('X-Pricing-Token', $pricingToken);
            
        } else {
            // Guest kullanıcılar için sadece temel bilgiler
            $response->headers->set('X-Customer-Type', 'GUEST');
            $response->headers->set('X-Is-Dealer', 'false');
        }
        
        return $response;
    }
    
    /**
     * 🔒 Token manipulation koruması için pricing token oluştur
     */
    private function generatePricingToken($user, string $customerType): string
    {
        $data = [
            'user_id' => $user->id,
            'customer_type' => $customerType,
            'is_approved_dealer' => $user->is_approved_dealer ?? false,
            'company_name' => $user->company_name,
            'tax_number' => $user->tax_number,
            'lifetime_value' => $user->lifetime_value ?? 0,
            'timestamp' => time(),
        ];
        
        // HMAC ile imzala (app key ile)
        $signature = hash_hmac('sha256', json_encode($data), config('app.key'));
        
        return base64_encode(json_encode($data) . '.' . $signature);
    }
}
