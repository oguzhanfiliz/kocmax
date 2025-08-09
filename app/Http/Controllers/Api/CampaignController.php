<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Kampanyalar",
 *     description="E-ticaret kampanya yönetimi API uç noktaları"
 * )
 */
class CampaignController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/campaigns",
     *     summary="Aktif kampanyaları listele",
     *     description="Müşteri tipine göre geçerli kampanyaları getirir",
     *     tags={"Kampanyalar"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Kampanya tipi filtresi",
     *         required=false,
     *         @OA\Schema(type="string", enum={"buy_x_get_y_free", "bundle_discount", "cross_sell"}, 
     *                    example="bundle_discount")
     *     ),
     *     @OA\Parameter(
     *         name="customer_type",
     *         in="query",
     *         description="Müşteri tipi (B2B: işletme, B2C: bireysel, Guest: misafir)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"b2b", "b2c", "guest"}, example="b2c")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Sayfa başına kampanya sayısı (maksimum: 50)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=50, default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="İstek başarıyla tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, 
     *                         description="İşlem durumu"),
     *             @OA\Property(property="message", type="string", example="Kampanyalar başarıyla getirildi",
     *                         description="Başarı mesajı"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Kampanya verileri",
     *                 @OA\Property(property="current_page", type="integer", example=1, 
     *                             description="Mevcut sayfa numarası"),
     *                 @OA\Property(property="per_page", type="integer", example=15,
     *                             description="Sayfa başına öğe sayısı"),
     *                 @OA\Property(property="total", type="integer", example=25,
     *                             description="Toplam kampanya sayısı"),
     *                 @OA\Property(
     *                     property="campaigns",
     *                     type="array",
     *                     description="Kampanya listesi",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1, description="Kampanya ID"),
     *                         @OA\Property(property="name", type="string", example="Kışlık İndirim Kampanyası",
     *                                     description="Kampanya adı"),
     *                         @OA\Property(property="slug", type="string", example="kislik-indirim-kampanyasi",
     *                                     description="URL dostu kampanya adı"),
     *                         @OA\Property(property="description", type="string", 
     *                                     example="Kışlık ürünlerde %30'a varan indirim fırsatı",
     *                                     description="Kampanya açıklaması"),
     *                         @OA\Property(property="type", type="string", example="bundle_discount",
     *                                     description="Kampanya türü"),
     *                         @OA\Property(property="status", type="string", example="active",
     *                                     description="Kampanya durumu"),
     *                         @OA\Property(property="priority", type="integer", example=1,
     *                                     description="Öncelik sırası (1 en yüksek)"),
     *                         @OA\Property(property="minimum_cart_amount", type="number", format="float", example=500.00,
     *                                     description="Minimum sepet tutarı (TL)"),
     *                         @OA\Property(property="starts_at", type="string", format="date-time", 
     *                                     example="2025-01-01T00:00:00.000000Z", description="Başlangıç tarihi"),
     *                         @OA\Property(property="ends_at", type="string", format="date-time", 
     *                                     example="2025-01-31T23:59:59.000000Z", description="Bitiş tarihi"),
     *                         @OA\Property(property="days_remaining", type="integer", example=15,
     *                                     description="Kalan gün sayısı"),
     *                         @OA\Property(property="usage_count", type="integer", example=125,
     *                                     description="Kullanım sayısı"),
     *                         @OA\Property(property="usage_limit", type="integer", example=1000,
     *                                     description="Kullanım limiti"),
     *                         @OA\Property(property="progress_percentage", type="number", format="float", example=12.5,
     *                                     description="İlerleme yüzdesi")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 50);
            $customerType = $request->get('customer_type', 'guest');
            $type = $request->get('type');

            // Kullanıcı giriş yapmışsa customer type'ı belirle
            if (Auth::check()) {
                $user = Auth::user();
                $customerType = $user->is_approved_dealer ? 'b2b' : 'b2c';
            }

            $query = Campaign::query()
                ->active()
                ->forCustomerType($customerType)
                ->orderBy('priority', 'asc')
                ->orderBy('created_at', 'desc');

            if ($type) {
                $query->byType($type);
            }

            $campaigns = $query->paginate($perPage);

            $campaignData = $campaigns->getCollection()->map(function (Campaign $campaign) {
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'slug' => $campaign->slug,
                    'description' => $campaign->description,
                    'type' => $campaign->type,
                    'status' => $campaign->status,
                    'priority' => $campaign->priority,
                    'minimum_cart_amount' => $campaign->minimum_cart_amount,
                    'starts_at' => $campaign->starts_at?->toISOString(),
                    'ends_at' => $campaign->ends_at?->toISOString(),
                    'days_remaining' => $campaign->days_remaining,
                    'usage_count' => $campaign->usage_count,
                    'usage_limit' => $campaign->usage_limit,
                    'progress_percentage' => $campaign->getProgressPercentage(),
                    'is_upcoming' => $campaign->isUpcoming(),
                    'is_expired' => $campaign->isExpired(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Kampanyalar başarıyla getirildi',
                'data' => [
                    'current_page' => $campaigns->currentPage(),
                    'per_page' => $campaigns->perPage(),
                    'total' => $campaigns->total(),
                    'last_page' => $campaigns->lastPage(),
                    'campaigns' => $campaignData,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanyalar getirilirken hata oluştu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/campaigns/{campaign}",
     *     summary="Kampanya detaylarını getir",
     *     description="Belirli bir kampanyanın tüm detaylarını ve kurallarını getirir",
     *     tags={"Kampanyalar"},
     *     @OA\Parameter(
     *         name="campaign",
     *         in="path",
     *         description="Kampanya ID'si veya URL dostu adı (slug)",
     *         required=true,
     *         @OA\Schema(type="string", example="kislik-indirim-kampanyasi"),
     *         style="simple"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kampanya detayları başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, 
     *                         description="İşlem durumu"),
     *             @OA\Property(property="message", type="string", example="Kampanya detayları getirildi",
     *                         description="Başarı mesajı"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Kampanya detay verileri",
     *                 @OA\Property(property="id", type="integer", example=1, description="Kampanya benzersiz kimliği"),
     *                 @OA\Property(property="name", type="string", example="Kışlık İndirim Kampanyası",
     *                             description="Kampanya başlığı"),
     *                 @OA\Property(property="slug", type="string", example="kislik-indirim-kampanyasi",
     *                             description="URL dostu kampanya adı"),
     *                 @OA\Property(property="description", type="string", 
     *                             example="Kışlık ürünlerde %30'a varan indirim fırsatı. Seçili ürünlerde geçerli.",
     *                             description="Kampanya detaylı açıklaması"),
     *                 @OA\Property(property="type", type="string", example="bundle_discount",
     *                             description="Kampanya türü"),
     *                 @OA\Property(property="rules", type="object", 
     *                             example={"discount_percentage": 30, "min_quantity": 2, "max_discount": 500},
     *                             description="Kampanya kuralları ve koşulları"),
     *                 @OA\Property(property="rewards", type="object", 
     *                             example={"type": "percentage", "value": 30, "description": "%30 indirim"},
     *                             description="Kampanya ödülleri ve faydaları"),
     *                 @OA\Property(property="conditions", type="object", 
     *                             example={"min_cart_amount": 500, "excluded_categories": ["sale"]},
     *                             description="Kampanya geçerlilik koşulları"),
     *                 @OA\Property(property="customer_types", type="array", 
     *                             @OA\Items(type="string"), example={"b2c", "guest"},
     *                             description="Geçerli müşteri tipleri"),
     *                 @OA\Property(property="is_stackable", type="boolean", example=false,
     *                             description="Diğer kampanyalarla birleştirilebilir mi"),
     *                 @OA\Property(property="products_count", type="integer", example=15,
     *                             description="Kampanyada yer alan ürün sayısı"),
     *                 @OA\Property(property="categories_count", type="integer", example=3,
     *                             description="Kampanyada yer alan kategori sayısı")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kampanya bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="İşlem durumu"),
     *             @OA\Property(property="message", type="string", example="Aradığınız kampanya bulunamadı",
     *                         description="Hata mesajı")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Kampanya erişim yetkisi yok",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bu kampanya sizin müşteri tipiniz için geçerli değil")
     *         )
     *     )
     * )
     */
    public function show(Request $request, string $campaign): JsonResponse
    {
        try {
            // ID veya slug ile kampanya ara
            $campaignModel = is_numeric($campaign) 
                ? Campaign::active()->find($campaign)
                : Campaign::active()->where('slug', $campaign)->first();

            if (!$campaignModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kampanya bulunamadı',
                ], 404);
            }

            // Müşteri tipi kontrolü
            $customerType = 'guest';
            if (Auth::check()) {
                $user = Auth::user();
                $customerType = $user->is_approved_dealer ? 'b2b' : 'b2c';
            }

            if (!$campaignModel->isApplicableForCustomerType($customerType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kampanya sizin için geçerli değil',
                ], 403);
            }

            $data = [
                'id' => $campaignModel->id,
                'name' => $campaignModel->name,
                'slug' => $campaignModel->slug,
                'description' => $campaignModel->description,
                'type' => $campaignModel->type,
                'status' => $campaignModel->status,
                'rules' => $campaignModel->rules,
                'rewards' => $campaignModel->rewards,
                'conditions' => $campaignModel->conditions,
                'customer_types' => $campaignModel->customer_types,
                'priority' => $campaignModel->priority,
                'is_stackable' => $campaignModel->is_stackable,
                'minimum_cart_amount' => $campaignModel->minimum_cart_amount,
                'starts_at' => $campaignModel->starts_at?->toISOString(),
                'ends_at' => $campaignModel->ends_at?->toISOString(),
                'days_remaining' => $campaignModel->days_remaining,
                'usage_count' => $campaignModel->usage_count,
                'usage_limit' => $campaignModel->usage_limit,
                'usage_limit_per_customer' => $campaignModel->usage_limit_per_customer,
                'progress_percentage' => $campaignModel->getProgressPercentage(),
                'is_upcoming' => $campaignModel->isUpcoming(),
                'is_expired' => $campaignModel->isExpired(),
                'products_count' => $campaignModel->products()->count(),
                'categories_count' => $campaignModel->categories()->count(),
            ];

            // Eğer kullanıcı giriş yapmışsa, bu kampanyayı kullanıp kullanamayacağını kontrol et
            if (Auth::check()) {
                $data['can_use'] = $campaignModel->canBeUsedBy(Auth::user());
                $data['user_usage_count'] = $campaignModel->usages()
                    ->where('user_id', Auth::id())
                    ->count();
            }

            return response()->json([
                'success' => true,
                'message' => 'Kampanya detayları getirildi',
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya detayları getirilirken hata oluştu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/campaigns/{campaign}/validate",
     *     summary="Kampanya geçerliliğini kontrol et",
     *     description="Mevcut sepet içeriğine göre kampanyanın uygulanabilir olup olmadığını kontrol eder ve indirim miktarını hesaplar",
     *     tags={"Kampanyalar"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="campaign",
     *         in="path",
     *         description="Kontrol edilecek kampanya ID'si",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Sepet bilgileri",
     *         @OA\JsonContent(
     *             @OA\Property(property="cart_total", type="number", format="float", example=750.00,
     *                         description="Sepet toplam tutarı (TL)"),
     *             @OA\Property(property="cart_items", type="array", 
     *                         description="Sepetteki ürünler",
     *                         @OA\Items(
     *                 @OA\Property(property="product_id", type="integer", example=1,
     *                             description="Ürün ID'si"),
     *                 @OA\Property(property="variant_id", type="integer", example=2,
     *                             description="Ürün varyant ID'si"),
     *                 @OA\Property(property="quantity", type="integer", example=3,
     *                             description="Ürün adedi"),
     *                 @OA\Property(property="price", type="number", format="float", example=250.00,
     *                             description="Ürün birim fiyatı (TL)")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kampanya geçerlilik kontrolü tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true,
     *                         description="İşlem durumu"),
     *             @OA\Property(property="message", type="string", example="Kampanya geçerliliği kontrol edildi",
     *                         description="İşlem açıklaması"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Geçerlilik sonuçları",
     *                 @OA\Property(property="is_valid", type="boolean", example=true,
     *                             description="Kampanya uygulanabilir mi"),
     *                 @OA\Property(property="applicable_discount", type="number", format="float", example=75.00,
     *                             description="Uygulanabilir indirim miktarı (TL)"),
     *                 @OA\Property(property="discount_type", type="string", example="percentage",
     *                             description="İndirim türü (percentage/fixed)"),
     *                 @OA\Property(property="reasons", type="array", @OA\Items(type="string"),
     *                             example={"Minimum sepet tutarı karşılanmadı", "Bu kategoride geçerli değil"},
     *                             description="Geçersizlik sebepleri (varsa)"),
     *                 @OA\Property(property="campaign", type="object",
     *                             description="Kampanya özet bilgileri",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Kışlık İndirim"),
     *                             @OA\Property(property="type", type="string", example="bundle_discount")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kampanya bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Belirtilen kampanya bulunamadı")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Kimlik doğrulama gerekli",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function validateCampaign(Request $request, int $campaignId): JsonResponse
    {
        try {
            $campaign = Campaign::active()->find($campaignId);

            if (!$campaign) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kampanya bulunamadı',
                ], 404);
            }

            $user = Auth::user();
            $customerType = $user && $user->is_approved_dealer ? 'b2b' : ($user ? 'b2c' : 'guest');

            $cartTotal = (float) $request->get('cart_total', 0);
            $cartItems = $request->get('cart_items', []);

            $isValid = true;
            $reasons = [];
            $applicableDiscount = 0;

            // Müşteri tipi kontrolü
            if (!$campaign->isApplicableForCustomerType($customerType)) {
                $isValid = false;
                $reasons[] = 'Bu kampanya sizin müşteri tipiniz için geçerli değil';
            }

            // Minimum sepet tutarı kontrolü
            if ($campaign->minimum_cart_amount && $cartTotal < $campaign->minimum_cart_amount) {
                $isValid = false;
                $reasons[] = "Minimum sepet tutarı: {$campaign->minimum_cart_amount} TL";
            }

            // Kullanım sınırı kontrolü
            if ($campaign->hasReachedUsageLimit()) {
                $isValid = false;
                $reasons[] = 'Kampanya kullanım sınırına ulaştı';
            }

            // Kullanıcı bazlı kullanım sınırı kontrolü
            if ($user && !$campaign->canBeUsedBy($user)) {
                $isValid = false;
                $reasons[] = 'Bu kampanyayı kullanım sınırınıza ulaştınız';
            }

            // Kampanya kurallarına göre indirim hesapla
            if ($isValid && $campaign->rewards) {
                $rewards = $campaign->rewards;
                
                if (isset($rewards['type'])) {
                    switch ($rewards['type']) {
                        case 'percentage':
                            $applicableDiscount = $cartTotal * ($rewards['value'] / 100);
                            break;
                        case 'fixed':
                            $applicableDiscount = $rewards['value'];
                            break;
                        case 'buy_x_get_y_free':
                            // Daha karmaşık hesaplama gerekir
                            $applicableDiscount = 0; // Basit implementasyon için
                            break;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Kampanya geçerliliği kontrol edildi',
                'data' => [
                    'is_valid' => $isValid,
                    'applicable_discount' => $applicableDiscount,
                    'discount_type' => $campaign->rewards['type'] ?? null,
                    'reasons' => $reasons,
                    'campaign' => [
                        'id' => $campaign->id,
                        'name' => $campaign->name,
                        'type' => $campaign->type,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya geçerliliği kontrol edilirken hata oluştu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
