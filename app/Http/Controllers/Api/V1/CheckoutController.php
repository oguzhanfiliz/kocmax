<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Checkout\SecureCheckoutService;
use App\Services\Payment\PaymentManager;
use App\Exceptions\Checkout\CheckoutException;
use App\Exceptions\Checkout\CheckoutValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Güvenli checkout API controller'ı
 * PayTR entegrasyonu ile temiz, basit checkout süreci
 */
class CheckoutController extends Controller
{
    public function __construct(
        private SecureCheckoutService $checkoutService,
        private PaymentManager $paymentManager
    ) {}

    /**
     * Güvenli checkout oturumu başlatır
     * Frontend'den sadece variant_id + quantity alır, fiyatları backend hesaplar
     * 
     * @OA\Post(
     *      path="/api/v1/checkout/initialize",
     *      operationId="initializeCheckout",
     *      tags={"Checkout"},
     *      summary="Güvenli checkout oturumu başlat",
     *      description="Sepet verilerini alır, backend'de güvenli fiyat hesaplayıp checkout oturumu oluşturur",
     *      security={{ "sanctum": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"cart_items", "shipping_address", "billing_address"},
     *              @OA\Property(
     *                  property="cart_items",
     *                  type="array",
     *                  description="Sepet kalemleri (sadece ID ve miktar)",
     *                  @OA\Items(
     *                      type="object",
     *                      required={"product_variant_id", "quantity"},
     *                      @OA\Property(property="product_variant_id", type="integer", example=123),
     *                      @OA\Property(property="quantity", type="integer", example=2)
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="shipping_address",
     *                  type="object",
     *                  description="Teslimat adresi (address_id veya manuel)",
     *                  oneOf={
     *                      @OA\Schema(
     *                          type="object",
     *                          @OA\Property(property="address_id", type="integer", example=789)
     *                      ),
     *                      @OA\Schema(
     *                          type="object",
     *                          @OA\Property(property="manual", type="object",
     *                              @OA\Property(property="name", type="string", example="Ahmet Yılmaz"),
     *                              @OA\Property(property="phone", type="string", example="+90555123456"),
     *                              @OA\Property(property="address", type="string", example="Atatürk Cad. No:123"),
     *                              @OA\Property(property="city", type="string", example="İstanbul")
     *                          )
     *                      )
     *                  }
     *              ),
     *              @OA\Property(property="billing_address", type="object", description="Fatura adresi (shipping ile aynı format)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Checkout oturumu başarıyla oluşturuldu",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="checkout_session_id", type="string", example="uuid-string"),
     *                  @OA\Property(property="total_amount", type="number", format="float", example=299.90),
     *                  @OA\Property(property="subtotal", type="number", format="float", example=349.90),
     *                  @OA\Property(property="total_discount", type="number", format="float", example=50.00),
     *                  @OA\Property(property="currency", type="string", example="TRY"),
     *                  @OA\Property(property="applied_discounts", type="array", @OA\Items(type="object")),
     *                  @OA\Property(property="expires_at", type="string", format="datetime"),
     *                  @OA\Property(property="item_breakdown", type="array", @OA\Items(type="object"))
     *              )
     *          )
     *      ),
     *      @OA\Response(response=400, description="Doğrulama hatası"),
     *      @OA\Response(response=422, description="Sepet kalemleri geçersiz")
     * )
     */
    public function initialize(Request $request): JsonResponse
    {
        try {
            // Request logging - debug için
            Log::info('Checkout initialize request received', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Input doğrulama (frontend payload'ına uygun)
            $validated = $request->validate([
                'cart_items' => 'required|array|min:1',
                'cart_items.*.product_variant_id' => 'required|integer|exists:product_variants,id',
                'cart_items.*.quantity' => 'required|integer|min:1|max:999',
                
                // Frontend direkt address gönderiyor, manual wrapper yok
                'shipping_address' => 'required|array',
                'shipping_address.name' => 'required|string|max:255',
                'shipping_address.phone' => 'required|string|max:20',
                'shipping_address.address' => 'required|string|max:500',
                'shipping_address.city' => 'required|string|max:100',
                'shipping_address.state' => 'sometimes|string|max:100',
                'shipping_address.zip' => 'sometimes|string|max:20',
                'shipping_address.country' => 'required|string|max:2',
                
                'billing_address' => 'required|array',
                'billing_address.name' => 'required|string|max:255',
                'billing_address.phone' => 'required|string|max:20',
                'billing_address.address' => 'required|string|max:500',
                'billing_address.city' => 'required|string|max:100',
                'billing_address.state' => 'sometimes|string|max:100',
                'billing_address.zip' => 'sometimes|string|max:20',
                'billing_address.country' => 'required|string|max:2',
            ]);

            $user = Auth::user();

            Log::info('Güvenli checkout başlatılıyor', [
                'user_id' => $user->id,
                'cart_items_count' => count($validated['cart_items']),
                'user_tier' => $user->pricingTier?->name
            ]);

            // Backend'de güvenli checkout oturumu oluştur
            $checkoutSession = $this->checkoutService->initializeCheckout(
                $validated['cart_items'],
                $user,
                [
                    'shipping' => $validated['shipping_address'],
                    'billing' => $validated['billing_address']
                ]
            );

            return response()->json([
                'success' => true,
                'data' => array_merge(
                    $checkoutSession->toArray(),
                    ['item_breakdown' => $checkoutSession->getItemBreakdown()]
                ),
                'message' => 'Checkout oturumu başarıyla oluşturuldu'
            ]);

        } catch (CheckoutValidationException $e) {
            Log::warning('Checkout doğrulama hatası', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'VALIDATION_ERROR'
            ], 422);

        } catch (CheckoutException $e) {
            Log::error('Checkout hatası', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'CHECKOUT_ERROR'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Beklenmeyen checkout hatası', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Checkout oturumu oluşturulamadı',
                'error_code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Ödeme sürecini başlatır (PayTR iframe)
     * 
     * @OA\Post(
     *      path="/api/v1/checkout/payment/initialize",
     *      operationId="initializePayment",
     *      tags={"Checkout"},
     *      summary="PayTR ödeme sürecini başlat",
     *      description="Checkout oturumu için PayTR iframe ödeme sürecini başlatır",
     *      security={{ "sanctum": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"checkout_session_id"},
     *              @OA\Property(property="checkout_session_id", type="string", example="uuid-string"),
     *              @OA\Property(property="payment_provider", type="string", example="paytr", description="Varsayılan: paytr"),
     *              @OA\Property(property="installment", type="integer", example=3, description="Taksit sayısı (0=tek çekim)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="PayTR iframe ödeme başarıyla başlatıldı",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="payment_token", type="string"),
     *                  @OA\Property(property="iframe_url", type="string", example="https://www.paytr.com/odeme/guvenli/token"),
     *                  @OA\Property(property="expires_at", type="string", format="datetime"),
     *                  @OA\Property(property="order_summary", type="object",
     *                      @OA\Property(property="order_number", type="string"),
     *                      @OA\Property(property="total_amount", type="number", format="float"),
     *                      @OA\Property(property="currency", type="string")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function initializePayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'checkout_session_id' => 'required|string',
                'payment_provider' => 'sometimes|string|in:paytr',
                'installment' => 'sometimes|integer|min:0|max:12'
            ]);

            $user = Auth::user();
            $sessionId = $validated['checkout_session_id'];
            $provider = $validated['payment_provider'] ?? 'paytr';

            // Checkout oturumunu doğrula
            $checkoutSession = $this->checkoutService->getCheckoutSession($sessionId, $user);

            // Fiyat doğrulama (son güvenlik kontrolü)
            if (!$this->checkoutService->validateCheckoutPricing($sessionId, $user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fiyat bilgileri güncel değil, checkout oturumunu yenileyiniz',
                    'error_code' => 'PRICE_CHANGED'
                ], 409);
            }

            $pendingOrder = $checkoutSession->getPendingOrder();

            Log::info('PayTR ödeme süreci başlatılıyor', [
                'user_id' => $user->id,
                'checkout_session_id' => $sessionId,
                'order_number' => $pendingOrder->order_number,
                'total_amount' => $checkoutSession->getTotalAmount(),
                'provider' => $provider
            ]);

            // PaymentManager ile ödeme başlat
            $paymentResult = $this->paymentManager->initializePayment($provider, $pendingOrder, [
                'max_installment' => $validated['installment'] ?? 0,
                'no_installment' => ($validated['installment'] ?? 0) == 0 ? 1 : 0
            ]);

            if ($paymentResult->isFailure()) {
                Log::error('PayTR ödeme başlatma başarısız', [
                    'order_number' => $pendingOrder->order_number,
                    'error' => $paymentResult->getErrorMessage()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ödeme başlatılamadı: ' . $paymentResult->getErrorMessage(),
                    'error_code' => $paymentResult->getErrorCode()
                ], 400);
            }

            Log::info('PayTR ödeme başarıyla başlatıldı', [
                'order_number' => $pendingOrder->order_number,
                'iframe_url' => $paymentResult->getIframeUrl()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_token' => $paymentResult->getToken(),
                    'iframe_url' => $paymentResult->getIframeUrl(),
                    'expires_at' => $paymentResult->getExpiresAt()?->format('Y-m-d H:i:s'),
                    'order_summary' => [
                        'order_number' => $pendingOrder->order_number,
                        'total_amount' => $checkoutSession->getTotalAmount(),
                        'currency' => $pendingOrder->currency_code,
                        'items_count' => $pendingOrder->items->count()
                    ],
                    'paytr_info' => $paymentResult->getMetadata()
                ],
                'message' => 'PayTR ödeme başarıyla başlatıldı'
            ]);

        } catch (CheckoutException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'CHECKOUT_SESSION_ERROR'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Ödeme başlatma hatası', [
                'user_id' => Auth::id(),
                'checkout_session_id' => $request->input('checkout_session_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ödeme başlatılamadı',
                'error_code' => 'PAYMENT_INITIALIZATION_ERROR'
            ], 500);
        }
    }

    /**
     * Checkout oturumunu doğrular ve detaylarını getirir
     */
    public function getSession(Request $request, string $sessionId): JsonResponse
    {
        try {
            $user = Auth::user();
            $checkoutSession = $this->checkoutService->getCheckoutSession($sessionId, $user);

            return response()->json([
                'success' => true,
                'data' => array_merge(
                    $checkoutSession->toArray(),
                    ['item_breakdown' => $checkoutSession->getItemBreakdown()]
                )
            ]);

        } catch (CheckoutException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'SESSION_NOT_FOUND'
            ], 404);
        }
    }

    /**
     * Checkout oturumunu iptal eder
     */
    public function cancelSession(string $sessionId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Session'ın varlığını ve sahipliğini kontrol et
            $checkoutSession = $this->checkoutService->getCheckoutSession($sessionId, $user);
            
            // Pending order'ı iptal et
            $pendingOrder = $checkoutSession->getPendingOrder();
            $pendingOrder->update(['status' => 'cancelled']);
            
            // Session'ı temizle
            $this->checkoutService->clearCheckoutSession($sessionId);

            Log::info('Checkout oturumu iptal edildi', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'order_number' => $pendingOrder->order_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout oturumu başarıyla iptal edildi'
            ]);

        } catch (CheckoutException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}