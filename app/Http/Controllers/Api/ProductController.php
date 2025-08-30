<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;
use App\Models\Product;
use App\Models\Category;
use App\Services\MultiCurrencyPricingService;
use App\Services\Pricing\CustomerTypeDetectorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Ürün kataloğu ve arama API uç noktaları - Public endpoints (Authentication not required)"
 * )
 */
class ProductController extends Controller
{
    private MultiCurrencyPricingService $pricingService;
    private CustomerTypeDetectorService $customerTypeDetector;

    public function __construct(
        MultiCurrencyPricingService $pricingService,
        CustomerTypeDetectorService $customerTypeDetector
    ) {
        $this->pricingService = $pricingService;
        $this->customerTypeDetector = $customerTypeDetector;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     operationId="getProducts",
     *     tags={"Products", "Public API"},
     *     summary="Filtreleme ve arama ile ürün listesini al (Public)",
     *     description="Gelişmiş filtreleme seçenekleriyle sayfalanmış ürün listesini döndürür. Akıllı fiyatlandırma sistemi ile müşteri tipine göre fiyatlar hesaplanır. Authentication opsiyonel - giriş yapmış kullanıcılar için kişiselleştirilmiş fiyatlar.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Ürün adı, açıklaması veya SKU için arama terimi",
     *         required=false,
     *         @OA\Schema(type="string", example="güvenlik ayakkabısı")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Kategori ID'sine göre filtrele",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="category_slug",
     *         in="query",
     *         description="Kategori slug'ına göre filtrele",
     *         required=false,
     *         @OA\Schema(type="string", example="is-guvenlik-ayakkabilari")
     *     ),
     *     @OA\Parameter(
     *         name="categories",
     *         in="query",
     *         description="Birden fazla kategori ID'sine göre filtrele (virgülle ayrılmış)",
     *         required=false,
     *         @OA\Schema(type="string", example="1,2,3")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum fiyat filtresi",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=50.00)
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maksimum fiyat filtresi",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=500.00)
     *     ),
     *     @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         description="Markaya göre filtrele",
     *         required=false,
     *         @OA\Schema(type="string", example="3M")
     *     ),
     *     @OA\Parameter(
     *         name="gender",
     *         in="query",
     *         description="Cinsiyete göre filtrele",
     *         required=false,
     *         @OA\Schema(type="string", enum={"male", "female", "unisex"}, example="unisex")
     *     ),
     *     @OA\Parameter(
     *         name="in_stock",
     *         in="query",
     *         description="Sadece stokta olan ürünleri filtrele",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Sadece öne çıkan ürünleri filtrele",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sıralama alanı",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "price", "created_at", "popularity"}, example="name")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sıralama düzeni",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Sayfa başına öğe sayısı",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=20)
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         description="Fiyat gösterimi için para birimi",
     *         required=false,
     *         @OA\Schema(type="string", enum={"TRY", "USD", "EUR"}, example="TRY")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="İstek başarıyla tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ürünler başarıyla getirildi",
     *                         description="Başarı mesajı"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=150),
     *                 @OA\Property(property="last_page", type="integer", example=8)
     *             ),
     *             @OA\Property(property="filters", type="object",
     *                 @OA\Property(property="applied", type="object"),
     *                 @OA\Property(property="available", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Doğrulama hatası",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Domain not allowed (production only)",
     *         @OA\JsonContent(ref="#/components/responses/DomainNotAllowed")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Rate limit exceeded (100 req/min for public endpoints)",
     *         @OA\JsonContent(ref="#/components/responses/RateLimitExceeded")
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'category_slug' => 'nullable|string|exists:categories,slug',
            'categories' => 'nullable|string',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'brand' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,unisex',
            'in_stock' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'sort' => 'nullable|in:name,price,created_at,popularity',
            'order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'currency' => 'nullable|in:TRY,USD,EUR',
        ]);

        // 🎯 Smart Pricing: Detect customer type (optional auth)
        $customerInfo = $this->customerTypeDetector->detectFromRequest($request);
        
        // Set context for resource transformation
        $this->setResourceContext($validated['currency'] ?? 'TRY', $customerInfo);

        $query = Product::with(['variants.images', 'categories', 'images', 'activeCertificates'])
            ->where('is_active', true);

        // Search functionality
        if (!empty($validated['search'])) {
            $searchTerm = $validated['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('brand', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('safety_standard', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Category filter
        if (!empty($validated['category_id'])) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $validated['category_id']));
        }

        // Category slug filter
        if (!empty($validated['category_slug'])) {
            $query->whereHas('categories', fn($q) => $q->where('categories.slug', $validated['category_slug']));
        }

        // Multiple categories filter
        if (!empty($validated['categories'])) {
            $categoryIds = array_map('intval', explode(',', $validated['categories']));
            $query->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds));
        }

        // Price filters
        if (isset($validated['min_price']) || isset($validated['max_price'])) {
            $query->whereHas('variants', function ($q) use ($validated) {
                if (isset($validated['min_price'])) {
                    $q->where('price', '>=', $validated['min_price']);
                }
                if (isset($validated['max_price'])) {
                    $q->where('price', '<=', $validated['max_price']);
                }
            });
        }

        // Brand filter
        if (!empty($validated['brand'])) {
            $query->where('brand', $validated['brand']);
        }

        // Gender filter
        if (!empty($validated['gender'])) {
            $query->where('gender', $validated['gender']);
        }

        // Stock filter
        if (isset($validated['in_stock']) && $validated['in_stock']) {
            $query->whereHas('variants', fn($q) => $q->where('stock', '>', 0));
        }

        // Featured filter
        if (isset($validated['featured']) && $validated['featured']) {
            $query->where('is_featured', true);
        }

        // Sorting
        $sort = $validated['sort'] ?? 'sort_order';
        $order = $validated['order'] ?? 'asc';

        switch ($sort) {
            case 'name':
                $query->orderBy('name', $order);
                break;
            case 'price':
                $query->orderBy('base_price', $order);
                break;
            case 'created_at':
                $query->orderBy('created_at', $order);
                break;
            case 'popularity':
                // Order by sales count or bestseller status
                $query->orderBy('is_bestseller', 'desc')
                      ->orderBy('created_at', $order);
                break;
            default:
                $query->orderBy('sort_order', 'asc')
                      ->orderBy('created_at', 'desc');
        }

        $perPage = min($validated['per_page'] ?? 20, 100);
        
        // 🚀 Smart caching based on customer type
        $cacheKey = $this->customerTypeDetector->getCacheKey(
            'products.list.' . md5(serialize($validated)),
            $customerInfo['type'],
            $customerInfo['user']?->id
        );
        
        // 🚀 Smart caching with cache tagging support check
        $shouldUseCache = $this->customerTypeDetector->isSmartPricingEnabled() 
            && config('features.smart_pricing_cache_enabled');
        
        if ($shouldUseCache) {
            $cacheTtl = config('features.smart_pricing_cache_ttl', 300);
            
            if (Cache::supportsTags()) {
                // Use cache tags if available (Redis/Memcached)
                $products = Cache::tags(['products', $customerInfo['type']])
                    ->remember($cacheKey, $cacheTtl, function() use ($query, $perPage) {
                        return $query->paginate($perPage);
                    });
            } else {
                // Fallback to simple caching without tags (File/Database cache)
                $products = Cache::remember($cacheKey, $cacheTtl, function() use ($query, $perPage) {
                    return $query->paginate($perPage);
                });
            }
        } else {
            $products = $query->paginate($perPage);
        }

        return ProductResource::collection($products)->additional([
            'message' => 'Ürünler başarıyla getirildi',
            'filters' => [
                'applied' => array_filter($validated),
                'available' => $this->getAvailableFilters(),
            ],
            'pricing_info' => $this->getPricingInfo($customerInfo),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     operationId="getProduct",
     *     tags={"Products", "Public API"},
     *     summary="Tek ürün ayrıntılarını al (Public)",
     *     description="Belirli bir ürün hakkında ayrıntılı bilgi döndürür. Akıllı fiyatlandırma sistemi ile müşteri tipine göre fiyatlar hesaplanır. Authentication opsiyonel - giriş yapmış kullanıcılar için kişiselleştirilmiş fiyatlar.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ürün ID'si veya slug",
     *         required=true,
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         description="Fiyat gösterimi için para birimi (şu anda sadece TRY destekleniyor)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"TRY"}, example="TRY")
     *     ),
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="Fiyat hesaplaması için adet (varsayılan: 1)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="İstek başarıyla tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ürün detayları başarıyla getirildi",
     *                         description="Başarı mesajı"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductDetail")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ürün bulunamadı",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Domain not allowed (production only)",
     *         @OA\JsonContent(ref="#/components/responses/DomainNotAllowed")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Rate limit exceeded",
     *         @OA\JsonContent(ref="#/components/responses/RateLimitExceeded")
     *     )
     * )
     */
    public function show(Request $request, Product $product): ProductDetailResource
    {
        $validated = $request->validate([
            'currency' => 'nullable|in:TRY,USD,EUR',
        ]);

        // 🎯 Smart Pricing: Detect customer type for detailed product
        $customerInfo = $this->customerTypeDetector->detectFromRequest($request);
        
        // Set context for detailed resource transformation
        $this->setResourceContext($validated['currency'] ?? 'TRY', $customerInfo);

        $product->load([
            'variants.images', 
            'categories', 
            'images', 
            'reviews.user',
            'variants.variantOptions.variantType',
            'activeCertificates'
        ]);

        return (new ProductDetailResource($product))->additional([
            'message' => 'Ürün detayları başarıyla getirildi',
            'pricing_info' => $this->getPricingInfo($customerInfo),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{product}/pricing",
     *     operationId="getProductPricing",
     *     tags={"Products", "Public API"},
     *     summary="Ürün fiyatlandırma bilgilerini al (Public)",
     *     description="Müşteri tipine göre ürün fiyatlandırma bilgilerini döndürür. Authentication opsiyonel.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Ürün ID veya slug",
     *         required=true,
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         description="Para birimi",
     *         required=false,
     *         @OA\Schema(type="string", enum={"TRY", "USD", "EUR"}, example="TRY")
     *     ),
     *     @OA\Parameter(
     *         name="context",
     *         in="query",
     *         description="Fiyatlandırma bağlamı (JSON)",
     *         required=false,
     *         @OA\Schema(type="string", example="order_quantity: 10")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="İstek başarıyla tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ürün fiyatlandırma bilgileri başarıyla getirildi"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Güvenlik Ayakkabısı"),
     *                 @OA\Property(property="slug", type="string", example="guvenlik-ayakkabisi"),
     *                 @OA\Property(property="pricing", type="object",
     *                     @OA\Property(property="base_price", type="number", format="float", example=150.00),
     *                     @OA\Property(property="your_price", type="number", format="float", example=127.50),
     *                     @OA\Property(property="currency", type="string", example="TRY"),
     *                     @OA\Property(property="base_price_formatted", type="string", example="150,00 ₺"),
     *                     @OA\Property(property="your_price_formatted", type="string", example="127,50 ₺"),
     *                     @OA\Property(property="discount_percentage", type="number", format="float", example=15.0),
     *                     @OA\Property(property="is_dealer_price", type="boolean", example=true),
     *                     @OA\Property(property="customer_type", type="string", example="b2b"),
     *                     @OA\Property(property="bulk_discounts", type="array", @OA\Items(
     *                         @OA\Property(property="min_quantity", type="integer", example=10),
     *                         @OA\Property(property="discount_percentage", type="number", format="float", example=5.0)
     *                     ))
     *                 ),
     *                 @OA\Property(property="variants", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="42 Numara Siyah"),
     *                     @OA\Property(property="price", type="number", format="float", example=150.00),
     *                     @OA\Property(property="stock", type="integer", example=25),
     *                     @OA\Property(property="color", type="string", example="Siyah"),
     *                     @OA\Property(property="size", type="string", example="42"),
     *                     @OA\Property(property="is_active", type="boolean", example=true)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ürün bulunamadı",
     *         @OA\JsonContent(ref="#/components/responses/NotFound")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/responses/ValidationError")
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
    public function pricing(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'currency' => 'nullable|in:TRY,USD,EUR',
            'context' => 'nullable|string',
        ]);

        // Parse context if provided
        $context = [];
        if (!empty($validated['context'])) {
            $context = json_decode($validated['context'], true) ?? [];
        }

        // 🎯 Smart Pricing: Detect customer type for pricing
        $customerInfo = $this->customerTypeDetector->detectFromRequest($request, $context);
        
        // Set context for resource transformation
        $this->setResourceContext($validated['currency'] ?? 'TRY', $customerInfo);

        $product->load(['variants.images', 'categories', 'images']);

        // Calculate pricing data
        $pricingData = $this->calculateProductPricing($product, $customerInfo, $validated['currency'] ?? 'TRY');

        return response()->json([
            'success' => true,
            'message' => 'Ürün fiyatlandırma bilgileri başarıyla getirildi',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'pricing' => $pricingData,
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'price' => app(\App\Services\CurrencyConversionService::class)->convertPrice(
                            (float) ($variant->source_price ?? $variant->price),
                            $variant->source_currency ?? ($variant->currency_code ?? 'TRY'),
                            'TRY'
                        ),
                        'stock' => (int) $variant->stock,
                        'color' => $variant->color,
                        'size' => $variant->size,
                        'is_active' => (bool) $variant->is_active,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Calculate product pricing based on customer type
     */
    private function calculateProductPricing(Product $product, array $customerInfo, string $currency = 'TRY'): array
    {
        $conversionService = app(\App\Services\CurrencyConversionService::class);
        
        // Convert base price to TRY
        $basePriceTRY = $conversionService->convertPrice(
            (float) $product->base_price,
            $product->base_currency ?? 'TRY',
            'TRY'
        );

        // Calculate customer-specific pricing
        $customerType = $customerInfo['type'] ?? 'guest';
        $isDealer = $customerInfo['is_dealer'] ?? false;
        
        // Base pricing
        $pricing = [
            'base_price' => $basePriceTRY,
            'your_price' => $basePriceTRY,
            'currency' => 'TRY',
            'base_price_formatted' => $this->formatPrice($basePriceTRY, 'TRY'),
            'your_price_formatted' => $this->formatPrice($basePriceTRY, 'TRY'),
            'discount_percentage' => 0,
            'is_dealer_price' => $isDealer,
            'customer_type' => $customerType,
            'bulk_discounts' => [],
        ];

        // Apply customer type discounts
        if ($customerType === 'b2b' || $customerType === 'wholesale') {
            $discountPercentage = $customerType === 'wholesale' ? 5.0 : 0.0;
            $pricing['discount_percentage'] = $discountPercentage;
            $pricing['your_price'] = $basePriceTRY * (1 - $discountPercentage / 100);
            $pricing['your_price_formatted'] = $this->formatPrice($pricing['your_price'], 'TRY');
        }

        // Add bulk discounts
        $pricing['bulk_discounts'] = [
            ['min_quantity' => 10, 'discount_percentage' => 5.0],
            ['min_quantity' => 50, 'discount_percentage' => 10.0],
            ['min_quantity' => 100, 'discount_percentage' => 15.0],
        ];

        return $pricing;
    }

    /**
     * Format price with currency
     */
    private function formatPrice(float $price, string $currency = 'TRY'): string
    {
        return number_format($price, 2, ',', '.') . ' ' . ($currency === 'TRY' ? '₺' : $currency);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/search-suggestions",
     *     operationId="getProductSuggestions",
     *     tags={"Products", "Public API"},
     *     summary="Arama önerilerini al (Public)",
     *     description="Kısmi girdiye göre arama önerilerini döndürür. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Arama sorgusu (minimum 2 karakter)",
     *         required=true,
     *         @OA\Schema(type="string", minLength=2, example="güv")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maksimum öneri sayısı",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=20, example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="İstek başarıyla tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true,
     *                         description="İşlem durumu"),
     *             @OA\Property(property="message", type="string", example="Arama önerileri başarıyla getirildi",
     *                         description="Başarı mesajı"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="products", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="image", type="string")
     *                 )),
     *                 @OA\Property(property="categories", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 )),
     *                 @OA\Property(property="brands", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $query = $validated['q'];
        $limit = $validated['limit'] ?? 10;

        // Product suggestions
        $products = Product::select('id', 'name', 'slug')
            ->with(['images' => fn($q) => $q->where('is_primary', true)->limit(1)])
            ->where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->images->first()?->image_url,
                ];
            });

        // Category suggestions
        $categories = Category::select('id', 'name', 'slug')
            ->where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            });

        // Brand suggestions
        $brands = Product::select('brand')
            ->where('is_active', true)
            ->where('brand', 'LIKE', "%{$query}%")
            ->whereNotNull('brand')
            ->distinct()
            ->limit(5)
            ->pluck('brand');

        return response()->json([
            'success' => true,
            'message' => 'Arama önerileri başarıyla getirildi',
            'data' => [
                'products' => $products,
                'categories' => $categories,
                'brands' => $brands,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/filters",
     *     operationId="getProductFilters",
     *     tags={"Products", "Public API"},
     *     summary="Mevcut filtreleri al (Public)",
     *     description="Ürünler için mevcut tüm filtre seçeneklerini döndürür. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="İstek başarıyla tamamlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true,
     *                         description="İşlem durumu"),
     *             @OA\Property(property="message", type="string", example="Filtreler başarıyla getirildi",
     *                         description="Başarı mesajı"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="categories", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="product_count", type="integer")
     *                 )),
     *                 @OA\Property(property="brands", type="array", @OA\Items(
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="product_count", type="integer")
     *                 )),
     *                 @OA\Property(property="price_ranges", type="array", @OA\Items(
     *                     @OA\Property(property="min", type="number"),
     *                     @OA\Property(property="max", type="number"),
     *                     @OA\Property(property="label", type="string")
     *                 ))
     *             )
     *         )
     *     )
     * )
     */
    public function filters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Filtreler başarıyla getirildi',
            'data' => $this->getAvailableFilters(),
        ]);
    }

    private function getAvailableFilters(): array
    {
        // Categories with product count
        $categories = Category::withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'products_count'])
            ->toArray();

        // Brands with product count
        $brands = Product::select('brand')
            ->selectRaw('COUNT(*) as product_count')
            ->where('is_active', true)
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->orderBy('brand')
            ->get()
            ->map(fn($item) => [
                'name' => $item->brand,
                'product_count' => $item->product_count,
            ])
            ->toArray();

        // Price ranges
        $priceRanges = [
            ['min' => 0, 'max' => 100, 'label' => '0₺ - 100₺'],
            ['min' => 100, 'max' => 250, 'label' => '100₺ - 250₺'],
            ['min' => 250, 'max' => 500, 'label' => '250₺ - 500₺'],
            ['min' => 500, 'max' => 1000, 'label' => '500₺ - 1000₺'],
            ['min' => 1000, 'max' => null, 'label' => '1000₺+'],
        ];

        return [
            'categories' => $categories,
            'brands' => $brands,
            'price_ranges' => $priceRanges,
            'genders' => [
                ['value' => 'male', 'label' => 'Erkek'],
                ['value' => 'female', 'label' => 'Kadın'],
                ['value' => 'unisex', 'label' => 'Unisex'],
            ],
        ];
    }

    /**
     * Set context for resource transformation (smart pricing)
     */
    private function setResourceContext(string $currency, array $customerInfo): void
    {
        // Currency context
        app()->instance('api_currency', $currency);
        
        // Customer context for smart pricing
        app()->instance('api_customer_info', $customerInfo);
        app()->instance('api_customer_type', $customerInfo['type']);
        app()->instance('api_authenticated_user', $customerInfo['user']);
        
        // Feature flags
        app()->instance('api_smart_pricing_enabled', $this->customerTypeDetector->isSmartPricingEnabled());
    }
    
    /**
     * Get pricing information for API response
     */
    private function getPricingInfo(array $customerInfo): array
    {
        $user = $customerInfo['user'];
        $discount = $this->customerTypeDetector->getDiscountPercentage($user);
        
        return [
            'customer_type' => $customerInfo['type'],
            'type_label' => $this->customerTypeDetector->getTypeLabel($customerInfo['type']),
            'is_authenticated' => $customerInfo['is_authenticated'],
            'is_dealer' => $customerInfo['is_dealer'],
            'discount_percentage' => $discount,
            'pricing_tier' => $user?->pricingTier ? [
                'id' => $user->pricingTier->id,
                'name' => $user->pricingTier->name,
                'discount' => $user->pricingTier->discount_percentage,
            ] : null,
            'smart_pricing_enabled' => $this->customerTypeDetector->isSmartPricingEnabled(),
        ];
    }
}