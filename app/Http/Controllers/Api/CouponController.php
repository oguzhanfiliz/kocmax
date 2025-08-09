<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Coupons",
 *     description="İndirim kuponları yönetimi API endpoints"
 * )
 */
class CouponController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/coupons/validate",
     *     summary="Kupon geçerliliğini kontrol et",
     *     description="Kupon kodunun geçerli olup olmadığını ve indirim miktarını hesaplar",
     *     tags={"Coupons"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", example="SUMMER25", description="Kupon kodu"),
     *             @OA\Property(property="cart_total", type="number", format="float", example=750.00, description="Sepet toplam tutarı")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Başarılı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kupon geçerliliği kontrol edildi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="is_valid", type="boolean", example=true),
     *                 @OA\Property(property="coupon_code", type="string", example="SUMMER25"),
     *                 @OA\Property(property="coupon_type", type="string", enum={"percentage", "fixed"}, example="percentage"),
     *                 @OA\Property(property="coupon_value", type="number", format="float", example=25.00),
     *                 @OA\Property(property="discount_amount", type="number", format="float", example=187.50),
     *                 @OA\Property(property="min_order_amount", type="number", format="float", example=200.00),
     *                 @OA\Property(property="remaining_usage", type="integer", example=45),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", nullable=true, example="2025-12-31T23:59:59.000000Z"),
     *                 @OA\Property(property="reasons", type="array", @OA\Items(type="string"), description="Geçersizlik sebepleri")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kupon bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kupon bulunamadı"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="is_valid", type="boolean", example=false),
     *                 @OA\Property(property="reasons", type="array", @OA\Items(type="string"), example={"Kupon kodu geçersiz"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasyon hatası",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasyon hatası"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function validateCoupon(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:50',
                'cart_total' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = strtoupper(trim($request->get('code')));
            $cartTotal = (float) $request->get('cart_total');

            $coupon = DiscountCoupon::where('code', $code)->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kupon bulunamadı',
                    'data' => [
                        'is_valid' => false,
                        'reasons' => ['Kupon kodu geçersiz'],
                    ],
                ], 404);
            }

            $reasons = [];
            $isValid = true;
            $discountAmount = 0;

            // Genel kupon geçerliliği
            if (!$coupon->isValid()) {
                $isValid = false;
                
                if (!$coupon->is_active) {
                    $reasons[] = 'Kupon pasif durumda';
                }
                
                if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                    $reasons[] = 'Kupon süresi dolmuş';
                }
                
                if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                    $reasons[] = 'Kupon kullanım sınırına ulaşmış';
                }
            }

            // Minimum sipariş tutarı kontrolü
            if ($isValid && !$coupon->canBeUsedForAmount($cartTotal)) {
                $isValid = false;
                $reasons[] = "Minimum sipariş tutarı: {$coupon->min_order_amount} TL";
            }

            // İndirim miktarını hesapla
            if ($isValid) {
                $discountAmount = $coupon->calculateDiscount($cartTotal);
            }

            $remainingUsage = null;
            if ($coupon->usage_limit) {
                $remainingUsage = max(0, $coupon->usage_limit - $coupon->used_count);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kupon geçerliliği kontrol edildi',
                'data' => [
                    'is_valid' => $isValid,
                    'coupon_code' => $coupon->code,
                    'coupon_type' => $coupon->type,
                    'coupon_value' => $coupon->value,
                    'discount_amount' => round($discountAmount, 2),
                    'min_order_amount' => $coupon->min_order_amount,
                    'remaining_usage' => $remainingUsage,
                    'expires_at' => $coupon->expires_at?->toISOString(),
                    'reasons' => $reasons,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon kontrol edilirken hata oluştu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/coupons/apply",
     *     summary="Kupon uygula",
     *     description="Geçerli kuponu sepete uygular ve kullanım sayısını artırır",
     *     tags={"Coupons"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", example="SUMMER25", description="Kupon kodu"),
     *             @OA\Property(property="cart_total", type="number", format="float", example=750.00, description="Sepet toplam tutarı"),
     *             @OA\Property(property="order_id", type="integer", nullable=true, example=123, description="Sipariş ID (opsiyonel)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kupon başarıyla uygulandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kupon başarıyla uygulandı"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="coupon_code", type="string", example="SUMMER25"),
     *                 @OA\Property(property="discount_amount", type="number", format="float", example=187.50),
     *                 @OA\Property(property="original_total", type="number", format="float", example=750.00),
     *                 @OA\Property(property="new_total", type="number", format="float", example=562.50),
     *                 @OA\Property(property="used_count", type="integer", example=51),
     *                 @OA\Property(property="remaining_usage", type="integer", nullable=true, example=49)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Kupon uygulanamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kupon uygulanamadı"),
     *             @OA\Property(property="reasons", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function apply(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:50',
                'cart_total' => 'required|numeric|min:0',
                'order_id' => 'nullable|integer|exists:orders,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = strtoupper(trim($request->get('code')));
            $cartTotal = (float) $request->get('cart_total');

            $coupon = DiscountCoupon::where('code', $code)->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kupon bulunamadı',
                    'reasons' => ['Kupon kodu geçersiz'],
                ], 404);
            }

            // Kupon geçerliliği kontrolü
            if (!$coupon->canBeUsedForAmount($cartTotal)) {
                $reasons = [];
                
                if (!$coupon->isValid()) {
                    if (!$coupon->is_active) $reasons[] = 'Kupon pasif durumda';
                    if ($coupon->expires_at && $coupon->expires_at->isPast()) $reasons[] = 'Kupon süresi dolmuş';
                    if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) $reasons[] = 'Kupon kullanım sınırına ulaşmış';
                }
                
                if ($coupon->min_order_amount && $cartTotal < $coupon->min_order_amount) {
                    $reasons[] = "Minimum sipariş tutarı: {$coupon->min_order_amount} TL";
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Kupon uygulanamadı',
                    'reasons' => $reasons,
                ], 400);
            }

            // İndirim hesapla ve kupon kullan
            $discountAmount = $coupon->calculateDiscount($cartTotal);
            $newTotal = $cartTotal - $discountAmount;

            // Kupon kullanımını artır
            $coupon->use();

            $remainingUsage = null;
            if ($coupon->usage_limit) {
                $remainingUsage = max(0, $coupon->usage_limit - $coupon->used_count);
            }

            // TODO: Order ile kupon ilişkisini kaydet (order_coupons tablosu gerekebilir)

            return response()->json([
                'success' => true,
                'message' => 'Kupon başarıyla uygulandı',
                'data' => [
                    'coupon_code' => $coupon->code,
                    'discount_amount' => round($discountAmount, 2),
                    'original_total' => $cartTotal,
                    'new_total' => round($newTotal, 2),
                    'used_count' => $coupon->used_count,
                    'remaining_usage' => $remainingUsage,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon uygulanırken hata oluştu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/coupons/my-coupons",
     *     summary="Kullanıcı kuponlarını listele",
     *     description="Kullanıcının sahip olduğu aktif kuponları listeler (admin tarafından atanmış)",
     *     tags={"Coupons"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Kupon durumu filtresi",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "expired", "used"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Başarılı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kuponlar listelendi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="code", type="string", example="WELCOME10"),
     *                     @OA\Property(property="type", type="string", enum={"percentage", "fixed"}, example="percentage"),
     *                     @OA\Property(property="value", type="number", format="float", example=10.00),
     *                     @OA\Property(property="min_order_amount", type="number", format="float", nullable=true, example=100.00),
     *                     @OA\Property(property="usage_limit", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="used_count", type="integer", example=0),
     *                     @OA\Property(property="expires_at", type="string", format="date-time", nullable=true, example="2025-12-31T23:59:59.000000Z"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="is_expired", type="boolean", example=false),
     *                     @OA\Property(property="is_used_up", type="boolean", example=false),
     *                     @OA\Property(property="remaining_usage", type="integer", nullable=true, example=1)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function myCoupons(Request $request): JsonResponse
    {
        try {
            // TODO: Bu özellik için user_coupons pivot tablosu gerekebilir
            // Şimdilik genel kuponları döndürelim
            
            $status = $request->get('status');
            
            $query = DiscountCoupon::query();
            
            switch ($status) {
                case 'active':
                    $query->valid();
                    break;
                case 'expired':
                    $query->where('expires_at', '<', now());
                    break;
                case 'used':
                    $query->whereColumn('used_count', '>=', 'usage_limit')
                          ->whereNotNull('usage_limit');
                    break;
                default:
                    $query->active(); // Varsayılan olarak aktif kuponlar
            }
            
            $coupons = $query->orderBy('created_at', 'desc')->get();
            
            $couponData = $coupons->map(function (DiscountCoupon $coupon) {
                $remainingUsage = null;
                if ($coupon->usage_limit) {
                    $remainingUsage = max(0, $coupon->usage_limit - $coupon->used_count);
                }
                
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'min_order_amount' => $coupon->min_order_amount,
                    'usage_limit' => $coupon->usage_limit,
                    'used_count' => $coupon->used_count,
                    'expires_at' => $coupon->expires_at?->toISOString(),
                    'is_active' => $coupon->is_active,
                    'is_expired' => $coupon->expires_at && $coupon->expires_at->isPast(),
                    'is_used_up' => $coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit,
                    'remaining_usage' => $remainingUsage,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Kuponlar listelendi',
                'data' => $couponData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kuponlar listelenirken hata oluştu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/coupons/public",
     *     summary="Genel kuponları listele",
     *     description="Herkese açık aktif kuponları listeler",
     *     tags={"Coupons"},
     *     @OA\Parameter(
     *         name="min_amount",
     *         in="query",
     *         description="Minimum sipariş tutarı filtresi",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=500.00)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Başarılı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Genel kuponlar listelendi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="code", type="string", example="FREESHIP"),
     *                     @OA\Property(property="type", type="string", enum={"percentage", "fixed"}, example="fixed"),
     *                     @OA\Property(property="value", type="number", format="float", example=50.00),
     *                     @OA\Property(property="min_order_amount", type="number", format="float", nullable=true, example=200.00),
     *                     @OA\Property(property="description", type="string", example="%25 indirim kuponunuz"),
     *                     @OA\Property(property="expires_at", type="string", format="date-time", nullable=true, example="2025-12-31T23:59:59.000000Z"),
     *                     @OA\Property(property="remaining_usage", type="integer", nullable=true, example=150)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function publicCoupons(Request $request): JsonResponse
    {
        try {
            $minAmount = $request->get('min_amount');
            
            $query = DiscountCoupon::valid()
                ->orderBy('value', 'desc')
                ->limit(10); // Sadece en iyi 10 kupon
            
            if ($minAmount) {
                $query->where(function ($q) use ($minAmount) {
                    $q->whereNull('min_order_amount')
                      ->orWhere('min_order_amount', '<=', $minAmount);
                });
            }
            
            $coupons = $query->get();
            
            $couponData = $coupons->map(function (DiscountCoupon $coupon) {
                $remainingUsage = null;
                if ($coupon->usage_limit) {
                    $remainingUsage = max(0, $coupon->usage_limit - $coupon->used_count);
                }
                
                $description = $coupon->type === 'percentage' 
                    ? "%{$coupon->value} indirim kuponunuz"
                    : "{$coupon->value} TL indirim kuponunuz";
                
                return [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'min_order_amount' => $coupon->min_order_amount,
                    'description' => $description,
                    'expires_at' => $coupon->expires_at?->toISOString(),
                    'remaining_usage' => $remainingUsage,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Genel kuponlar listelendi',
                'data' => $couponData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Genel kuponlar listelenirken hata oluştu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}