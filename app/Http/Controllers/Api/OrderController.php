<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\Cart;
use App\Services\Order\OrderService;
use App\Services\Checkout\CheckoutCoordinator;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        private CheckoutCoordinator $checkoutCoordinator
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
}