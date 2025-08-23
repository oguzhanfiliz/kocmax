<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use App\Services\Cart\CartStrategyFactory;
use App\Services\MultiCurrencyPricingService;
use App\Services\Campaign\CampaignEngine;
use App\ValueObjects\Campaign\CartContext;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\UpdateQuantityRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartSummaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="Alışveriş sepeti yönetimi ve sepet işlemleri API uç noktaları - Protected endpoints (Authentication required)"
 * )
 */
class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CartStrategyFactory $strategyFactory,
        private MultiCurrencyPricingService $multiCurrencyPricingService,
        private CampaignEngine $campaignEngine
    ) {}

    /**
     * @OA\Get(
     *      path="/api/v1/cart",
     *      operationId="getCart",
     *      tags={"Cart", "Protected API"},
     *      summary="Kullanıcının mevcut sepetini görüntüle",
     *      description="Giriş yapmış veya misafir kullanıcının sepetini para birimi dönüştürme seçeneğiyle getirir",
     *      @OA\Parameter(
     *          name="currency",
     *          description="Hedef para birimi kodu (örn: TRY, USD, EUR)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", example="USD")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Sepet başarıyla getirildi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="cart", type="object"),
     *                  @OA\Property(property="summary", type="object")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Sepet getirilemedi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Sepet getirilemedi")
     *          )
     *      )
     * )
     * 
     * Kullanıcının mevcut sepetini getir
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $targetCurrency = $request->get('_currency', $request->get('currency', 'TRY'));
            
            // Calculate summary with currency conversion
            $summary = $this->cartService->calculateSummary($cart, $targetCurrency);

            return response()->json([
                'success' => true,
                'data' => [
                    'cart' => new CartResource($cart->load(['items.productVariant']), $targetCurrency),
                    'summary' => new CartSummaryResource($summary),
                    'currency' => $targetCurrency,
                    'available_currencies' => $this->multiCurrencyPricingService->getAvailableCurrencies()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Sepet getirme hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sepet getirilemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/cart/items",
     *      operationId="addItemToCart",
     *      tags={"Cart", "Protected API"},
     *      summary="Sepete ürün ekle",
     *      description="Kullanıcının sepetine bir ürün varyantı ekleyin",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"product_variant_id"},
     *              @OA\Property(property="product_variant_id", type="integer", example=1),
     *              @OA\Property(property="quantity", type="integer", example=2, minimum=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Ürün başarıyla sepete eklendi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Ürün başarıyla sepete eklendi")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Ürün sepete eklenemedi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Ürün sepete eklenemedi")
     *          )
     *      )
     * )
     * 
     * Sepete ürün ekle
     */
    public function addItem(AddItemRequest $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $variant = ProductVariant::findOrFail($request->product_variant_id);
            $quantity = $request->quantity ?? 1;

            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->addItem($cart, $variant, $quantity);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla sepete eklendi',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Sepete ürün ekleme hatası', [
                'error' => $e->getMessage(),
                'variant_id' => $request->product_variant_id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ürün sepete eklenemedi',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/cart/items/{item}",
     *      operationId="updateCartItem",
     *      tags={"Cart", "Protected API"},
     *      summary="Sepetteki ürün miktarını güncelle",
     *      description="Mevcut bir sepet öğesinin miktarını güncelleyin",
     *      @OA\Parameter(
     *          name="item",
     *          description="Sepet öğesi ID'si",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"quantity"},
     *              @OA\Property(property="quantity", type="integer", example=3, minimum=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Sepet öğesi başarıyla güncellendi"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Sepet öğesi güncellenemedi"
     *      )
     * )
     * 
     * Sepetteki ürün miktarını güncelle
     */
    public function updateItem(UpdateQuantityRequest $request, int $itemId): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $item = $cart->items()->findOrFail($itemId);
            $quantity = $request->quantity;

            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->updateQuantity($cart, $item, $quantity);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Sepet öğesi başarıyla güncellendi',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Sepet öğesi güncelleme hatası', [
                'error' => $e->getMessage(),
                'item_id' => $itemId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sepet öğesi güncellenemedi',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/cart/items/{item}",
     *      operationId="removeCartItem",
     *      tags={"Cart", "Protected API"},
     *      summary="Ürünü sepetten kaldır",
     *      description="Kullanıcının sepetinden bir ürünü kaldırın",
     *      @OA\Parameter(
     *          name="item",
     *          description="Sepet öğesi ID'si",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Ürün başarıyla sepetten kaldırıldı"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Ürün sepetten kaldırılamadı"
     *      )
     * )
     * 
     * Sepetten ürün kaldır
     */
    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $item = $cart->items()->findOrFail($itemId);

            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->removeItem($cart, $item);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla sepetten kaldırıldı',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Sepetten ürün kaldırma hatası', [
                'error' => $e->getMessage(),
                'item_id' => $itemId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ürün sepetten kaldırılamadı',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/cart",
     *      operationId="clearCart",
     *      tags={"Cart", "Protected API"},
     *      summary="Tüm sepeti temizle",
     *      description="Kullanıcının sepetindeki tüm ürünleri kaldırın",
     *      @OA\Response(
     *          response=200,
     *          description="Sepet başarıyla temizlendi"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Sepet temizlenemedi"
     *      )
     * )
     * 
     * Tüm sepeti temizle
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);

            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->clearCart($cart);

            return response()->json([
                'success' => true,
                'message' => 'Sepet başarıyla temizlendi',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items'])),
                    'summary' => new CartSummaryResource($this->cartService->calculateSummary($cart))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Sepet temizleme hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sepet temizlenemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/cart/summary",
     *      operationId="getCartSummary",
     *      tags={"Cart", "Protected API"},
     *      summary="Sepet özetini al",
     *      description="Fiyatlandırma bilgileriyle sepet özetini alın",
     *      @OA\Response(
     *          response=200,
     *          description="Sepet özeti başarıyla getirildi"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Sepet özeti getirilemedi"
     *      )
     * )
     * 
     * Sadece sepet özetini getir
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'data' => new CartSummaryResource($summary)
            ]);

        } catch (\Exception $e) {
            Log::error('Sepet özeti getirme hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sepet özeti getirilemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/cart/refresh-pricing",
     *      operationId="refreshCartPricing",
     *      tags={"Cart", "Protected API"},
     *      summary="Sepet fiyatlarını yenile",
     *      description="Mevcut sepetteki ürünlerin fiyatlarını güncel değerlerle yenileyin",
     *      @OA\Response(
     *          response=200,
     *          description="Sepet fiyatları başarıyla yenilendi"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Sepet fiyatları yenilenemedi"
     *      )
     * )
     * 
     * Sepet fiyatlarını yenile
     */
    public function refreshPricing(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            
            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->refreshPricing($cart);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Sepet fiyatları başarıyla yenilendi',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Sepet fiyat yenileme hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sepet fiyatları yenilenemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/cart/migrate",
     *      operationId="migrateGuestCart",
     *      tags={"Cart", "Protected API"},
     *      summary="Misafir sepetini kimliği doğrulanmış kullanıcıya taşı",
     *      description="Ürünleri misafir sepetinden kimliği doğrulanmış kullanıcı sepetine aktarın",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Misafir sepeti başarıyla taşındı"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Sepeti taşımak için giriş yapılmalıdır"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Misafir sepeti taşınamadı"
     *      )
     * )
     * 
     * Misafir sepetini kimliği doğrulanmış kullanıcıya taşı
     */
    public function migrate(Request $request): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sepeti taşımak için giriş yapılmalıdır'
                ], 401);
            }

            $sessionId = $request->session()->getId();
            $user = Auth::user();

            $cart = $this->cartService->migrateGuestCart($sessionId, $user);

            if (!$cart) {
                return response()->json([
                    'success' => true,
                    'message' => 'Taşınacak misafir sepeti bulunamadı',
                    'data' => null
                ]);
            }

            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Misafir sepeti başarıyla taşındı',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Misafir sepeti taşıma hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Misafir sepeti taşınamadı',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/cart/apply-campaigns",
     *      operationId="applyCartCampaigns",
     *      tags={"Cart", "Protected API"},
     *      summary="Sepete uygulanabilir kampanyaları uygula",
     *      description="Sepet içeriğine göre uygulanabilir kampanyaları otomatik olarak uygular",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Kampanyalar başarıyla uygulandı",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="applied_campaigns", type="array", @OA\Items(type="object")),
     *                  @OA\Property(property="total_discount", type="number", example=25.50),
     *                  @OA\Property(property="free_shipping", type="boolean", example=true),
     *                  @OA\Property(property="cart_summary", type="object")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Kampanyalar uygulanamadı"
     *      )
     * )
     * 
     * Sepete uygulanabilir kampanyaları uygula
     */
    public function applyCampaigns(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $user = Auth::user();
            
            // Sepet içeriğini CartContext'e dönüştür
            $cartItems = $cart->items->map(function ($item) {
                return [
                    'product_id' => $item->productVariant->product_id,
                    'variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ];
            })->toArray();

            $customerType = $user && $user->is_approved_dealer ? 'b2b' : ($user ? 'b2c' : 'guest');
            
            $context = new CartContext(
                items: $cartItems,
                totalAmount: $cart->total_amount,
                customerType: $customerType,
                customerId: $user?->id
            );

            // Kampanyaları uygula
            $campaignResults = $this->campaignEngine->applyCampaigns($context, $user);
            
            $appliedCampaigns = [];
            $totalDiscount = 0;
            $freeShipping = false;

            foreach ($campaignResults as $result) {
                $campaign = $result['campaign'];
                $campaignResult = $result['result'];
                
                if ($campaignResult->isApplied()) {
                    $appliedCampaigns[] = [
                        'id' => $campaign->id,
                        'name' => $campaign->name,
                        'type' => $campaign->type,
                        'description' => $campaignResult->getMessage(),
                        'discount_amount' => $campaignResult->getDiscount()?->getAmount() ?? 0,
                        'discount_type' => $campaignResult->getDiscount()?->getType() ?? 'unknown'
                    ];

                    $totalDiscount += $campaignResult->getDiscount()?->getAmount() ?? 0;
                    
                    // Ücretsiz kargo kontrolü
                    if ($campaign->type === 'free_shipping') {
                        $freeShipping = true;
                    }
                }
            }

            // Sepet özetini güncelle
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Kampanyalar başarıyla uygulandı',
                'data' => [
                    'applied_campaigns' => $appliedCampaigns,
                    'total_discount' => $totalDiscount,
                    'free_shipping' => $freeShipping,
                    'cart_summary' => new CartSummaryResource($summary),
                    'campaigns_count' => count($appliedCampaigns)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Kampanya uygulama hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Kampanyalar uygulanamadı',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mevcut istek için sepeti getir veya oluştur
     */
    private function getOrCreateCart(Request $request): Cart
    {
        if (Auth::check()) {
            // Kimliği doğrulanmış kullanıcı sepeti
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'total_amount' => 0,
                    'discounted_amount' => 0,
                    'subtotal_amount' => 0,
                ]
            );
        } else {
            // Misafir sepeti
            $sessionId = $request->session()->getId();
            return Cart::firstOrCreate(
                ['session_id' => $sessionId],
                [
                    'total_amount' => 0,
                    'discounted_amount' => 0,
                    'subtotal_amount' => 0,
                ]
            );
        }
    }
}