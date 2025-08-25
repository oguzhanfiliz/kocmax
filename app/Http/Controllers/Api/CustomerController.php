<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Pricing\CustomerTypeDetectorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Customer",
 *     description="Müşteri tipi ve profil API uç noktaları"
 * )
 */
class CustomerController extends Controller
{
    private CustomerTypeDetectorService $customerTypeDetector;

    public function __construct(CustomerTypeDetectorService $customerTypeDetector)
    {
        $this->customerTypeDetector = $customerTypeDetector;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/customer/type",
     *     operationId="getCustomerType",
     *     tags={"Customer", "Public API"},
     *     summary="Müşteri tipini belirle (Public)",
     *     description="Kullanıcının müşteri tipini ve fiyatlandırma bilgilerini döndürür. Authentication opsiyonel.",
     *     security={{"domain_protection": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="İstek başarıyla tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Müşteri tipi başarıyla belirlendi"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="customer_type", type="string", enum={"b2b", "b2c", "wholesale", "retail", "guest"}, example="b2b"),
     *                 @OA\Property(property="can_access_dealer_prices", type="boolean", example=true),
     *                 @OA\Property(property="tier", type="string", example="b2b_gold"),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=5.0),
     *                 @OA\Property(property="is_authenticated", type="boolean", example=true),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="company_name", type="string", example="ABC Şirketi"),
     *                 @OA\Property(property="tax_number", type="string", example="1234567890")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Domain not allowed",
     *         @OA\JsonContent(ref="#/components/responses/DomainNotAllowed")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Rate limit exceeded",
     *         @OA\JsonContent(ref="#/components/responses/RateLimitExceeded")
     *     )
     * )
     */
    public function type(Request $request): JsonResponse
    {
        // Detect customer type from request
        $customerInfo = $this->customerTypeDetector->detectFromRequest($request);
        
        // Get customer tier
        $tier = $this->customerTypeDetector->getCustomerTier($customerInfo['user']);
        
        // Calculate discount percentage based on customer type
        $discountPercentage = $this->calculateDiscountPercentage($customerInfo['type'], $tier);
        
        return response()->json([
            'success' => true,
            'message' => 'Müşteri tipi başarıyla belirlendi',
            'data' => [
                'customer_type' => $customerInfo['type'],
                'can_access_dealer_prices' => $customerInfo['is_dealer'],
                'tier' => $tier,
                'discount_percentage' => $discountPercentage,
                'is_authenticated' => $customerInfo['is_authenticated'],
                'user_id' => $customerInfo['user']?->id,
                'company_name' => $customerInfo['user']?->company_name,
                'tax_number' => $customerInfo['user']?->tax_number,
            ],
        ]);
    }

    /**
     * Calculate discount percentage based on customer type and tier
     */
    private function calculateDiscountPercentage(string $customerType, string $tier): float
    {
        $baseDiscount = match($customerType) {
            'b2b' => 0.0,
            'wholesale' => 5.0,
            'b2c', 'retail', 'guest' => 0.0,
            default => 0.0,
        };

        // Add tier-based discounts
        $tierDiscount = match($tier) {
            'b2b_vip' => 10.0,
            'b2b_premium' => 7.5,
            'b2b_gold' => 5.0,
            'b2b_silver' => 2.5,
            'b2c_vip' => 3.0,
            'b2c_gold' => 2.0,
            'b2c_silver' => 1.0,
            default => 0.0,
        };

        return $baseDiscount + $tierDiscount;
    }
}
