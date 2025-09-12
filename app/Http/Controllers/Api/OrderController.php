<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Services\Order\OrderService;
use App\Services\Checkout\CheckoutCoordinator;
use App\Services\Payment\PaymentManager;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Pricing\PricingException;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Sipariş yönetimi API uç noktaları"
 * )
 */
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CheckoutCoordinator $checkoutCoordinator,
        private PaymentManager $paymentManager
    ) {}

    /**
     * @OA\Get(
     *      path="/api/v1/orders",
     *      operationId="getOrders",
     *      tags={"Orders"},
     *      summary="Siparişleri listele",
     *      description="Kimliği doğrulanmış kullanıcı için isteğe bağlı filtreleme ile siparişleri listeler",
     *      security={{ "sanctum": {} }},
     *      @OA\Parameter(
     *          name="status",
     *          description="Sipariş durumuna göre filtrele",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="from_date",
     *          description="Başlangıç tarihinden itibaren siparişleri filtrele (YYYY-MM-DD)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", format="date")
     *      ),
     *      @OA\Parameter(
     *          name="to_date",
     *          description="Bitiş tarihine kadar siparişleri filtrele (YYYY-MM-DD)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", format="date")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          description="Sayfa başına sipariş sayısı (varsayılan: 15)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="integer", default=15)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Siparişler başarıyla getirildi"
     *      )
     * )
     * 
     * Kimliği doğrulanmış kullanıcının siparişlerini listeler
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = Order::with(['items.product', 'items.productVariant'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->get('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->get('to_date'));
        }

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => OrderResource::collection($orders->items()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/orders/{order}",
     *      operationId="getOrder",
     *      tags={"Orders"},
     *      summary="Sipariş detaylarını getir",
     *      description="Belirli bir siparişin detaylı bilgilerini getirir",
     *      security={{ "sanctum": {} }},
     *      @OA\Parameter(
     *          name="order",
     *          description="Sipariş kimliği",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Sipariş detayları başarıyla getirildi"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Bu siparişi görüntüleme yetkiniz yok"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Sipariş bulunamadı"
     *      )
     * )
     * 
     * Belirtilen siparişin detaylarını gösterir
     */
    public function show(Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        $order->load(['items.product', 'items.productVariant', 'statusHistory']);

        return response()->json([
            'data' => new OrderResource($order)
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/orders",
     *      operationId="createOrder",
     *      tags={"Orders"},
     *      summary="Sipariş oluştur",
     *      description="Kullanıcının sepetinden ödeme işlemi ile birlikte yeni sipariş oluşturur",
     *      security={{ "sanctum": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"shipping_address", "billing_address"},
     *              @OA\Property(property="shipping_address", type="object", description="Teslimat adresi"),
     *              @OA\Property(property="billing_address", type="object", description="Fatura adresi"),
     *              @OA\Property(property="payment_method", type="string", enum={"card", "credit", "bank_transfer"}, description="Ödeme yöntemi"),
     *              @OA\Property(property="notes", type="string", description="Sipariş notları")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Sipariş başarıyla oluşturuldu"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Sepet boş veya doğrulama başarısız"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Ödeme doğrulaması başarısız"
     *      )
     * )
     * 
     * Sepetten yeni sipariş oluşturur (ödeme süreci)
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = Auth::user();
        
        // Get user's cart
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart || $cart->items()->count() === 0) {
            return response()->json([
                'message' => 'Sepet boş veya bulunamadı'
            ], 400);
        }

        try {
            // Validate checkout data
            $validation = $this->checkoutCoordinator->validateCheckout($cart, $request->validated());
            
            if (!$validation->isValid()) {
                return response()->json([
                    'message' => 'Ödeme doğrulaması başarısız oldu',
                    'errors' => $validation->getErrors(),
                    'warnings' => $validation->getWarnings()
                ], 422);
            }

            // Process checkout
            $order = $this->checkoutCoordinator->processCheckout($cart, $request->validated());
            
            $order->load(['items.product', 'items.productVariant']);

            return response()->json([
                'message' => 'Sipariş başarıyla oluşturuldu',
                'data' => new OrderResource($order)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sipariş oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/orders/guest-checkout",
     *      operationId="guestCheckout",
     *      tags={"Orders"},
     *      summary="Misafir alışveriş",
     *      description="Kullanıcı girişi yapmadan misafir olarak sipariş oluşturur",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"cart_data", "shipping_address", "billing_address"},
     *              @OA\Property(property="cart_data", type="object", description="Sepet verileri",
     *                  @OA\Property(property="items", type="array", @OA\Items(type="object"), description="Sepet kalemleri")
     *              ),
     *              @OA\Property(property="shipping_address", type="object", description="Teslimat adresi"),
     *              @OA\Property(property="billing_address", type="object", description="Fatura adresi"),
     *              @OA\Property(property="customer_email", type="string", format="email", description="Müşteri e-posta adresi"),
     *              @OA\Property(property="payment_method", type="string", enum={"card", "credit", "bank_transfer"}, description="Ödeme yöntemi")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Misafir siparişi başarıyla oluşturuldu"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Misafir alışveriş için sepet verisi gerekli"
     *      )
     * )
     * 
     * Misafir alışverişi işler (kimlik doğrulama gerekmez)
     */
    public function guestCheckout(StoreOrderRequest $request): JsonResponse
    {
        $cartData = $request->input('cart_data', []);
        $checkoutData = $request->except(['cart_data']);

        if (empty($cartData) || empty($cartData['items'])) {
            return response()->json([
                'message' => 'Misafir alışveriş için sepet verisi gereklidir'
            ], 400);
        }

        try {
            $order = $this->checkoutCoordinator->processGuestCheckout($cartData, $checkoutData);
            
            $order->load(['items.product', 'items.productVariant']);

            return response()->json([
                'message' => 'Misafir siparişi başarıyla oluşturuldu',
                'data' => new OrderResource($order)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Misafir alışveriş işlenemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sipariş durumunu günceller (sadece yönetici)
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('updateStatus', $order);

        $newStatus = OrderStatus::from($request->input('status'));
        $notes = $request->input('notes');

        try {
            $updated = $this->orderService->updateStatus($order, $newStatus, Auth::user(), $notes);

            if (!$updated) {
                return response()->json([
                    'message' => 'Durum değişikliğine izin verilmiyor'
                ], 400);
            }

            $order->load(['items.product', 'items.productVariant', 'statusHistory']);

            return response()->json([
                'message' => 'Sipariş durumu başarıyla güncellendi',
                'data' => new OrderResource($order)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sipariş durumu güncellenemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Siparişi iptal eder
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('cancel', $order);

        $reason = $request->input('reason', 'Müşteri iptali');

        try {
            $cancelled = $this->orderService->cancelOrder($order, Auth::user(), $reason);

            if (!$cancelled) {
                return response()->json([
                    'message' => 'Sipariş bu aşamada iptal edilemez'
                ], 400);
            }

            $order->load(['items.product', 'items.productVariant', 'statusHistory']);

            return response()->json([
                'message' => 'Sipariş başarıyla iptal edildi',
                'data' => new OrderResource($order)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sipariş iptal edilemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sipariş için ödeme işlemini gerçekleştirir
     */
    public function processPayment(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('processPayment', $order);

        $paymentData = $request->validate([
            'payment_method' => 'required|string|in:card,credit,bank_transfer',
            'payment_data' => 'required|array',
            'payment_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            $paymentAmount = $paymentData['payment_amount'] ?? $order->total_amount;
            
            $result = $this->orderService->processPayment(
                $order,
                $paymentData['payment_method'],
                $paymentAmount,
                $paymentData['payment_data']
            );

            if ($result->isSuccess()) {
                $order->load(['items.product', 'items.productVariant']);

                return response()->json([
                    'message' => 'Ödeme başarıyla işlendi',
                    'data' => new OrderResource($order),
                    'payment_result' => [
                        'transaction_id' => $result->getTransactionId(),
                        'amount' => $result->getAmount(),
                        'method' => $result->getMethod()
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => 'Ödeme işlemi başarısız oldu',
                    'error' => $result->getErrorMessage()
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ödeme işlemi hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/orders/{order}/tracking",
     *      operationId="getOrderTracking",
     *      tags={"Orders"},
     *      summary="Sipariş takibi",
     *      description="Sipariş numarasını kullanarak siparişin takip detaylarını ve durumunu getirir",
     *      @OA\Parameter(
     *          name="order",
     *          description="Sipariş numarası",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Takip bilgileri başarıyla getirildi",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="order_number", type="string", description="Sipariş numarası"),
     *                  @OA\Property(property="status", type="string", description="Sipariş durumu"),
     *                  @OA\Property(property="tracking_number", type="string", description="Kargo takip numarası"),
     *                  @OA\Property(property="shipping_carrier", type="string", description="Kargo firması"),
     *                  @OA\Property(property="estimated_delivery", type="string", format="date-time", description="Tahmini teslimat tarihi")
     *              )
     *          )
     *      )
     * )
     * 
     * Sipariş takip bilgilerini getirir
     */
    public function tracking(Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        $trackingInfo = [
            'order_number' => $order->order_number,
            'status' => $order->status->value,
            'status_label' => $order->status->getLabel(),
            'tracking_number' => $order->tracking_number,
            'shipping_carrier' => $order->shipping_carrier,
            'estimated_delivery' => $order->estimated_delivery_at,
            'shipped_at' => $order->shipped_at,
            'delivered_at' => $order->delivered_at,
            'history' => $order->statusHistory->map(function ($history) {
                return [
                    'status' => $history->status,
                    'status_label' => OrderStatus::from($history->status)->getLabel(),
                    'notes' => $history->notes,
                    'changed_by' => $history->user?->name,
                    'changed_at' => $history->created_at
                ];
            })
        ];

        return response()->json([
            'data' => $trackingInfo
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/orders/user/summary",
     *      operationId="getUserOrderSummary",
     *      tags={"Orders"},
     *      summary="Sipariş özeti",
     *      description="Kimliği doğrulanmış kullanıcının sipariş istatistikleri ve özetini getirir",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Sipariş özeti başarıyla getirildi",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="total_orders", type="integer", description="Toplam sipariş sayısı"),
     *                  @OA\Property(property="total_spent", type="number", format="float", description="Toplam harcama tutarı"),
     *                  @OA\Property(property="recent_orders", type="array", @OA\Items(type="object"), description="Son siparişler"),
     *                  @OA\Property(property="status_counts", type="object", description="Duruma göre sipariş sayıları")
     *              )
     *          )
     *      )
     * )
     * 
     * Mevcut kullanıcının sipariş özeti ve istatistiklerini getirir
     */
    public function summary(): JsonResponse
    {
        $user = Auth::user();
        
        $summary = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_spent' => Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'recent_orders' => Order::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'order_number', 'status', 'total_amount', 'created_at']),
            'status_counts' => Order::where('user_id', $user->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
        ];

        return response()->json([
            'data' => $summary
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/orders/estimate-checkout",
     *      operationId="estimateCheckout",
     *      tags={"Orders"},
     *      summary="Alışveriş tahmini",
     *      description="Sipariş oluşturmadan önce kargo ve vergiler dahil toplam maliyeti hesaplar",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"shipping_country", "shipping_city"},
     *              @OA\Property(property="shipping_country", type="string", maxLength=2, example="TR", description="Teslimat ülkesi kodu"),
     *              @OA\Property(property="billing_country", type="string", maxLength=2, example="TR", description="Fatura ülkesi kodu"),
     *              @OA\Property(property="shipping_city", type="string", example="Istanbul", description="Teslimat şehri"),
     *              @OA\Property(property="payment_method", type="string", enum={"card", "credit", "bank_transfer"}, description="Ödeme yöntemi")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Alışveriş tahmini başarıyla hesaplandı"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Sepet boş veya bulunamadı"
     *      )
     * )
     * 
     * Sipariş oluşturmadan önce alışveriş maliyetlerini tahmin eder
     */
    public function estimateCheckout(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart || $cart->items()->count() === 0) {
            return response()->json([
                'message' => 'Sepet boş veya bulunamadı'
            ], 400);
        }

        $checkoutData = $request->validate([
            'shipping_country' => 'required|string|max:2',
            'billing_country' => 'nullable|string|max:2',
            'shipping_city' => 'required|string|max:255',
            'payment_method' => 'nullable|string|in:card,credit,bank_transfer'
        ]);

        try {
            $estimate = $this->checkoutCoordinator->estimateCheckout($cart, $checkoutData);

            return response()->json([
                'data' => $estimate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Alışveriş tahmini hesaplanamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Frontend sepet ödeme işlemi - External API simülasyonu
     * Frontend'den sepet verileriyle sipariş oluşturur
     */
    public function processCheckoutPayment(Request $request): JsonResponse
    {
        try {
            // Validation - production için product_variant_id zorunlu
            $validated = $request->validate([
                'cart_items' => 'required|array|min:1',
                'cart_items.*.product_variant_id' => 'required|integer|exists:product_variants,id',
                'cart_items.*.quantity' => 'required|integer|min:1',
                
                // Address seçim yöntemi - address_id veya manual address
                'shipping_address_id' => 'nullable|integer|exists:addresses,id',
                'billing_address_id' => 'nullable|integer|exists:addresses,id',
                
                // Manuel address bilgileri (eğer address_id yoksa)
                'shipping_address' => 'required_without:shipping_address_id|array',
                'shipping_address.name' => 'required_with:shipping_address|string',
                'shipping_address.phone' => 'required_with:shipping_address|string',
                'shipping_address.address' => 'required_with:shipping_address|string',
                'shipping_address.city' => 'required_with:shipping_address|string',
                
                'billing_address' => 'required_without:billing_address_id|array',
                'billing_address.name' => 'required_with:billing_address|string',
                'billing_address.phone' => 'required_with:billing_address|string',
                'billing_address.address' => 'required_with:billing_address|string',
                'billing_address.city' => 'required_with:billing_address|string',
            ]);

            $user = Auth::user();
            
            Log::info('Checkout payment process started', [
                'user_id' => $user->id,
                'cart_items_count' => count($validated['cart_items'])
            ]);

            // 1. Sepet toplam tutarını hesapla
            try {
                $cartTotal = $this->calculateCartTotal($validated['cart_items']);
            } catch (\RuntimeException $e) {
                Log::warning('Cart total calculation failed due to missing price', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error_code' => 'PRICE_MISSING'
                ], 422);
            }
            
            // 2. External payment API simülasyonu
            $paymentSuccess = $this->simulateExternalPaymentAPI();
            
            if (!$paymentSuccess) {
                Log::warning('Payment simulation failed', ['user_id' => $user->id]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Ödeme işlemi başarısız oldu. Lütfen tekrar deneyiniz.',
                    'error_code' => 'PAYMENT_FAILED'
                ], 400);
            }

            // 3. Sipariş oluştur (henüz ödeme yapılmadı)
            try {
                $order = $this->createOrderFromCart($validated, $user, $cartTotal);
            } catch (\RuntimeException $e) {
                Log::warning('Order creation failed due to missing price', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error_code' => 'PRICE_MISSING'
                ], 422);
            }

            Log::info('Order created successfully from checkout payment', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $user->id,
                'total_amount' => $order->total_amount
            ]);

            // 4. PayTR ödeme sürecini başlat
            $paymentResult = $this->paymentManager->initializePayment('paytr', $order, [
                'max_installment' => 0, // Taksit yok
                'non_3d' => false, // 3D Secure aktif
                'test_mode' => config('payments.providers.paytr.test_mode', true)
            ]);

            if ($paymentResult->isSuccess()) {
                Log::info('PayTR payment initialized successfully', [
                    'order_number' => $order->order_number,
                    'iframe_url' => $paymentResult->getIframeUrl(),
                    'expires_at' => $paymentResult->getExpiresAt()?->format('Y-m-d H:i:s')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Sipariş oluşturuldu. Ödeme sayfasına yönlendiriliyorsunuz.',
                    'data' => [
                        'order' => [
                            'order_number' => $order->order_number,
                            'total_amount' => number_format((float) $order->total_amount, 2),
                            'status' => $order->status,
                            'payment_status' => $order->payment_status,
                            'created_at' => $order->created_at->format('Y-m-d H:i:s')
                        ],
                        'payment' => [
                            'provider' => 'paytr',
                            'iframe_url' => $paymentResult->getIframeUrl(),
                            'expires_at' => $paymentResult->getExpiresAt()?->format('Y-m-d H:i:s'),
                            'time_to_expiry_seconds' => $paymentResult->getMetadata()['time_to_expiry_seconds'] ?? null,
                            'requires_iframe' => true
                        ]
                    ]
                ], 201);
            } else {
                Log::error('PayTR payment initialization failed', [
                    'order_number' => $order->order_number,
                    'error' => $paymentResult->getErrorMessage()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ödeme sistemi başlatılamadı. Lütfen tekrar deneyiniz.',
                    'error_code' => 'PAYMENT_INITIALIZATION_FAILED',
                    'data' => [
                        'order' => [
                            'order_number' => $order->order_number,
                            'total_amount' => number_format((float) $order->total_amount, 2),
                            'status' => $order->status,
                            'payment_status' => 'pending'
                        ]
                    ]
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gönderilen veriler eksik veya hatalı.',
                'errors' => $e->errors(),
                'error_code' => 'VALIDATION_FAILED'
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Checkout payment process failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ödeme işlemi sırasında bir hata oluştu. Lütfen tekrar deneyiniz.',
                'error_code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * External Payment API simülasyonu
     * Gerçek implementasyonda burada external API çağrısı yapılacak
     */
    private function simulateExternalPaymentAPI(): bool
    {
        Log::info('Simulating external payment API call');
        
        // Simülasyon - %95 başarı oranı
        // Gerçek implementasyonda external API'ye istek atılacak
        $success = rand(1, 100) <= 95;
        
        Log::info('External payment API simulation result', ['success' => $success]);
        
        return $success;
    }

    /**
     * Sepet toplam tutarını hesapla
     */
    private function calculateCartTotal(array $cartItems): float
    {
        $total = 0.0;
        $user = \Illuminate\Support\Facades\Auth::user();
        $pricing = app(\App\Services\PricingService::class);

        foreach ($cartItems as $item) {
            $variant = ProductVariant::with('product')->find($item['product_variant_id']);
            if ($variant) {
                $qty = (int) $item['quantity'];
                try {
                    $priceResult = $pricing->calculatePrice($variant, $qty, $user);
                    $total += $priceResult->getTotalFinalPrice()->getAmount();
                } catch (PricingException $e) {
                    // Dönüştür ve üst seviyede 422 için yakalat
                    Log::warning('Price missing for cart item', [
                        'variant_id' => $variant->id,
                        'quantity' => $qty,
                        'user_id' => $user?->id,
                        'error' => $e->getMessage()
                    ]);
                    throw new \RuntimeException('Sepette fiyatı hesaplanamayan bir ürün var. Lütfen sepetinizi kontrol edin.');
                }
            }
        }

        return $total;
    }

    /**
     * Sepet verilerinden sipariş oluştur
     */
    private function createOrderFromCart(array $validated, $user, float $totalAmount): Order
    {
        // Shipping ve billing address bilgilerini resolve et
        $shippingAddressData = $this->resolveAddress($validated, 'shipping', $user);
        $billingAddressData = $this->resolveAddress($validated, 'billing', $user);
        
        // Kargo ücretini hesapla (şimdilik sabit veya config)
        $shippingAmount = $this->computeShippingAmount($shippingAddressData, $billingAddressData, $user);

        // Sepet kalemlerini fiyat kurallarıyla yeniden hesapla (indirim toplamını ölçmek için)
        $pricing = app(\App\Services\PricingService::class);
        $orderDiscountTotal = 0.0;
        foreach ($validated['cart_items'] as $ci) {
            $variant = ProductVariant::with('product')->find($ci['product_variant_id']);
            if (!$variant) { continue; }
            $pr = $pricing->calculatePrice($variant, (int) $ci['quantity'], $user);
            $orderDiscountTotal += $pr->getTotalDiscountAmount()->getAmount();
        }

        // Sipariş verilerini hazırla
        $orderData = [
            'user_id' => $user->id,
            'order_number' => $this->generateOrderNumber(),
            'subtotal' => $totalAmount,
            'shipping_amount' => $shippingAmount,
            'tax_amount' => 0,
            'discount_amount' => $orderDiscountTotal,
            'total_amount' => $totalAmount + $shippingAmount,
            'currency_code' => 'TRY',
            'status' => 'pending',
            'payment_method' => 'online',
            'payment_status' => 'pending', // Henüz ödeme yapılmadı
            'customer_type' => $user->customer_type ?? 'B2C', // Customer type ekle
            
            // Shipping address
            'shipping_name' => $shippingAddressData['name'],
            'shipping_email' => $user->email, // User email'i kullan
            'shipping_phone' => $shippingAddressData['phone'],
            'shipping_address' => $shippingAddressData['address'],
            'shipping_city' => $shippingAddressData['city'],
            'shipping_state' => $shippingAddressData['state'] ?? '',
            'shipping_zip' => $shippingAddressData['zip'] ?? '',
            'shipping_country' => $shippingAddressData['country'] ?? 'TR',
            
            // Billing address  
            'billing_name' => $billingAddressData['name'],
            'billing_email' => $user->email, // User email'i kullan
            'billing_phone' => $billingAddressData['phone'],
            'billing_address' => $billingAddressData['address'],
            'billing_city' => $billingAddressData['city'],
            'billing_state' => $billingAddressData['state'] ?? '',
            'billing_zip' => $billingAddressData['zip'] ?? '',
            'billing_country' => $billingAddressData['country'] ?? 'TR',
            'billing_tax_number' => $billingAddressData['tax_number'] ?? null,
            'billing_tax_office' => $billingAddressData['tax_office'] ?? null,
        ];

        // Order oluştur
        $order = Order::create($orderData);
        
        // Order items oluştur (pricing ile)
        $pricing = app(\App\Services\PricingService::class);
        foreach ($validated['cart_items'] as $item) {
            $variant = ProductVariant::with('product')->find($item['product_variant_id']);
            
            if ($variant && $variant->product) {
                $qty = (int) $item['quantity'];
                $priceResult = $pricing->calculatePrice($variant, $qty, $user);
                $unitFinal = $priceResult->getUnitFinalPrice()->getAmount();
                $lineFinal = $priceResult->getTotalFinalPrice()->getAmount();
                $lineDiscount = $priceResult->getTotalDiscountAmount()->getAmount();

                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $qty,
                    'price' => $unitFinal,
                    'discount_amount' => $lineDiscount,
                    'tax_amount' => 0,
                    'total' => $lineFinal,
                    'product_name' => $variant->product->name,
                    'product_sku' => $variant->sku ?? '',
                    'product_attributes' => [
                        'color' => $variant->color,
                        'size' => $variant->size
                    ]
                ]);
                
                Log::info('Order item created', [
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'unit_price' => $unitFinal,
                    'quantity' => $qty
                ]);
            } else {
                Log::warning('ProductVariant or Product not found', [
                    'product_variant_id' => $item['product_variant_id'],
                    'variant_exists' => $variant ? true : false,
                    'product_exists' => $variant && $variant->product ? true : false
                ]);
            }
        }
        
        return $order;
    }

    /**
     * Variant fiyatını resolve eder: variant.price yoksa product.base_price
     */
    private function resolveVariantUnitPrice(\App\Models\ProductVariant $variant): ?float
    {
        // Önce TRY'ye çevrilmiş birim fiyatı dene
        try {
            $converted = $variant->getPriceInCurrency('TRY');
            if ($converted !== null) {
                return (float) $converted;
            }
        } catch (\Throwable $e) {
            // ignore and try fallbacks
        }
        // Fallback: variant.price
        if ($variant->price !== null) {
            return (float) $variant->price;
        }
        // Fallback: product.base_price
        if ($variant->product && $variant->product->base_price !== null) {
            return (float) $variant->product->base_price;
        }
        return null;
    }

    /**
     * Basit kargo ücreti hesaplama (şimdilik sabit)
     */
    private function computeShippingAmount(array $shippingAddress, array $billingAddress, $user): float
    {
        return (float) (config('shipping.flat_rate', 30.0));
    }

    /**
     * Address resolve et - ID'den veya manual data'dan
     */
    private function resolveAddress(array $validated, string $type, $user): array
    {
        $addressIdKey = $type . '_address_id';
        $addressDataKey = $type . '_address';
        
        // Eğer address_id varsa o adresi kullan
        if (!empty($validated[$addressIdKey])) {
            $address = $user->addresses()->find($validated[$addressIdKey]);
            
            if (!$address) {
                throw new \Exception("Seçilen {$type} adresi bulunamadı.");
            }
            
            // Address modelinden data'ya çevir
            return [
                'name' => $address->full_name,
                'phone' => $address->phone ?? '',
                'address' => $address->address_line_1 . ($address->address_line_2 ? ', ' . $address->address_line_2 : ''),
                'city' => $address->city,
                'state' => $address->state ?? '',
                'zip' => $address->postal_code ?? '',
                'country' => $address->country ?? 'TR',
                'tax_number' => $address->company_name ? '' : null, // Şirket varsa vergi no olabilir
                'tax_office' => null
            ];
        }
        
        // Manuel address data kullan
        if (!empty($validated[$addressDataKey])) {
            return [
                'name' => $validated[$addressDataKey]['name'],
                'phone' => $validated[$addressDataKey]['phone'] ?? '',
                'address' => $validated[$addressDataKey]['address'],
                'city' => $validated[$addressDataKey]['city'],
                'state' => $validated[$addressDataKey]['state'] ?? '',
                'zip' => $validated[$addressDataKey]['zip'] ?? '',
                'country' => $validated[$addressDataKey]['country'] ?? 'TR',
                'tax_number' => $validated[$addressDataKey]['tax_number'] ?? null,
                'tax_office' => $validated[$addressDataKey]['tax_office'] ?? null
            ];
        }
        
        throw new \Exception("Geçerli bir {$type} adresi sağlanmalıdır.");
    }

    /**
     * Sipariş numarası oluştur (PayTR uyumlu - sadece alfanumerik)
     */
    private function generateOrderNumber(): string
    {
        return 'ORD' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
