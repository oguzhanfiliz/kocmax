<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;
use App\Models\Product;
use App\Models\Category;
use App\Services\MultiCurrencyPricingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Ürün kataloğu ve arama API uç noktaları - Public endpoints (Authentication not required)"
 * )
 */
class ProductController extends Controller
{
    private MultiCurrencyPricingService $pricingService;

    public function __construct(MultiCurrencyPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     operationId="getProducts",
     *     tags={"Products", "Public API"},
     *     summary="Filtreleme ve arama ile ürün listesini al (Public)",
     *     description="Gelişmiş filtreleme seçenekleriyle sayfalanmış ürün listesini döndürür. Authentication gerektirmez, guest pricing kullanır. Production'da domain koruması aktiftir.",
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

        $query = Product::with(['variants', 'categories', 'images'])
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
        $products = $query->paginate($perPage);

        // Set currency context for resource transformation
        $currency = $validated['currency'] ?? 'TRY';
        app()->instance('api_currency', $currency);

        return ProductResource::collection($products)->additional([
            'message' => 'Ürünler başarıyla getirildi',
            'filters' => [
                'applied' => array_filter($validated),
                'available' => $this->getAvailableFilters(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     operationId="getProduct",
     *     tags={"Products", "Public API"},
     *     summary="Tek ürün ayrıntılarını al (Public)",
     *     description="Belirli bir ürün hakkında ayrıntılı bilgi döndürür. Authentication gerektirmez, guest pricing kullanır.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ürün ID'si",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
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

        $currency = $validated['currency'] ?? 'TRY';
        app()->instance('api_currency', $currency);

        $product->load([
            'variants.images', 
            'categories', 
            'images', 
            'reviews.user',
            'variants.variantOptions.variantType'
        ]);

        return (new ProductDetailResource($product))->additional([
            'message' => 'Ürün detayları başarıyla getirildi',
        ]);
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
}