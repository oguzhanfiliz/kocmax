<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Pricing\CustomerTypeDetectorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Pricing System",
 *     description="Akıllı Fiyatlandırma Sistemi API uç noktaları"
 * )
 */
class PricingSystemController extends Controller
{
    private CustomerTypeDetectorService $customerTypeDetector;

    public function __construct(CustomerTypeDetectorService $customerTypeDetector)
    {
        $this->customerTypeDetector = $customerTypeDetector;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/pricing/customer-types",
     *     operationId="getCustomerTypes",
     *     tags={"Pricing System"},
     *     summary="Müşteri tiplerini listele",
     *     description="Sistemde tanımlı müşteri tiplerini ve özelliklerini döndürür.",
     *     @OA\Response(
     *         response=200,
     *         description="Müşteri tipleri başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Müşteri tipleri başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="type", type="string", example="B2C", description="Müşteri tipi kodu"),
     *                 @OA\Property(property="label", type="string", example="👤 Bireysel Fiyat", description="Görüntülenecek etiket"),
     *                 @OA\Property(property="description", type="string", example="Bireysel müşteriler için fiyatlandırma", description="Açıklama"),
     *                 @OA\Property(property="is_dealer", type="boolean", example=false, description="Bayi mi"),
     *                 @OA\Property(property="requires_auth", type="boolean", example=false, description="Kimlik doğrulama gerektirir mi")
     *             ))
     *         )
     *     )
     * )
     */
    public function getCustomerTypes(): JsonResponse
    {
        $customerTypes = [
            [
                'type' => 'B2C',
                'label' => '👤 Bireysel Fiyat',
                'description' => 'Bireysel müşteriler için fiyatlandırma (giriş yapmış ve yapmamış)',
                'is_dealer' => false,
                'requires_auth' => false
            ],
            [
                'type' => 'B2B',
                'label' => '🏢 Bayi Fiyatı',
                'description' => 'Onaylı bayiler için fiyatlandırma',
                'is_dealer' => true,
                'requires_auth' => true
            ],
            [
                'type' => 'WHOLESALE',
                'label' => '📦 Toptan Fiyat',
                'description' => 'Toptan satış müşterileri için fiyatlandırma',
                'is_dealer' => true,
                'requires_auth' => true
            ],
            [
                'type' => 'RETAIL',
                'label' => '🛍️ Perakende Fiyat',
                'description' => 'Perakende satış noktaları için fiyatlandırma',
                'is_dealer' => false,
                'requires_auth' => true
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Müşteri tipleri başarıyla getirildi',
            'data' => $customerTypes
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/pricing/rules",
     *     operationId="getPricingRules",
     *     tags={"Pricing System"},
     *     summary="Aktif fiyatlandırma kurallarını listele",
     *     description="Sistemde tanımlı aktif fiyatlandırma kurallarını döndürür.",
     *     @OA\Response(
     *         response=200,
     *         description="Fiyatlandırma kuralları başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Fiyatlandırma kuralları başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=23),
     *                 @OA\Property(property="name", type="string", example="Perakende iskonto"),
     *                 @OA\Property(property="customer_types", type="array", @OA\Items(type="string", example="b2c")),
     *                 @OA\Property(property="min_quantity", type="integer", example=1),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=35.0),
     *                 @OA\Property(property="priority", type="integer", example=1),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             ))
     *         )
     *     )
     * )
     */
    public function getPricingRules(): JsonResponse
    {
        $rules = \App\Models\PricingRule::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get()
            ->map(function ($rule) {
                $customerTypes = $this->normalizeCustomerTypes($rule->conditions['customer_types'] ?? []);

                return [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'customer_types' => $customerTypes,
                    'min_quantity' => $rule->conditions['min_quantity'] ?? 1,
                    'discount_percentage' => $rule->actions['discount_percentage'] ?? 0,
                    'priority' => $rule->priority,
                    'is_active' => $rule->is_active
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Fiyatlandırma kuralları başarıyla getirildi',
            'data' => $rules
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/pricing/calculate",
     *     operationId="calculatePricing",
     *     tags={"Pricing System"},
     *     summary="Fiyat hesaplama testi",
     *     description="Belirli bir ürün için müşteri tipine göre fiyat hesaplaması yapar.",
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Ürün ID'si",
     *         required=true,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="customer_type",
     *         in="query",
     *         description="Müşteri tipi",
     *         required=false,
     *         @OA\Schema(type="string", enum={"B2C", "B2B", "WHOLESALE", "RETAIL"}, example="B2C")
     *     ),
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="Adet",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fiyat hesaplaması başarıyla tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Fiyat hesaplaması başarıyla tamamlandı"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="product_id", type="integer", example=15),
     *                 @OA\Property(property="base_price", type="number", format="float", example=155.00),
     *                 @OA\Property(property="your_price", type="number", format="float", example=100.75),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=35.0),
     *                 @OA\Property(property="customer_type", type="string", example="B2C"),
     *                 @OA\Property(property="quantity", type="integer", example=1),
     *                 @OA\Property(property="applied_rule", type="object", nullable=true,
     *                     @OA\Property(property="id", type="integer", example=23),
     *                     @OA\Property(property="name", type="string", example="Perakende iskonto")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function calculatePricing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'customer_type' => 'nullable|in:B2C,B2B,WHOLESALE,RETAIL',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = \App\Models\Product::find($validated['product_id']);
        $customerType = $validated['customer_type'] ?? 'B2C';
        $quantity = $validated['quantity'] ?? 1;

        // Mock user for testing
        $user = null;
        if ($customerType === 'B2B') {
            $user = new \stdClass();
            $user->is_approved_dealer = true;
        }

        $basePrice = (float) $product->base_price;
        $discountPercentage = $this->customerTypeDetector->getDiscountPercentage($user, $quantity);
        $discountAmount = $basePrice * ($discountPercentage / 100);
        $yourPrice = $basePrice - $discountAmount;

        // Find applied rule
        $appliedRule = \App\Models\PricingRule::where('is_active', true)
            ->where('conditions->customer_types', 'like', '%"' . strtolower($customerType) . '"%')
            ->where('conditions->min_quantity', '<=', $quantity)
            ->orderBy('priority', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Fiyat hesaplaması başarıyla tamamlandı',
            'data' => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'base_price' => $basePrice,
                'your_price' => $yourPrice,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'customer_type' => $customerType,
                'quantity' => $quantity,
                'applied_rule' => $appliedRule ? [
                    'id' => $appliedRule->id,
                    'name' => $appliedRule->name
                ] : null
            ]
        ]);
    }

    /**
     * Normalize customer type list by removing duplicates while preserving order.
     *
     * @param array $types
     * @return array
     */
    private function normalizeCustomerTypes(array $types): array
    {
        $normalized = [];
        $seen = [];

        foreach ($types as $type) {
            if (!is_string($type) || $type === '') {
                continue;
            }

            $key = strtolower($type);

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $normalized[] = $type;
            }
        }

        return $normalized;
    }
}
