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
 *     description="Product catalog and search operations"
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
     *     tags={"Products"},
     *     summary="Get product list with filtering and search",
     *     description="Returns paginated list of products with advanced filtering options",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for product name, description, or SKU",
     *         required=false,
     *         @OA\Schema(type="string", example="güvenlik ayakkabısı")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="categories",
     *         in="query",
     *         description="Filter by multiple category IDs (comma separated)",
     *         required=false,
     *         @OA\Schema(type="string", example="1,2,3")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum price filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=50.00)
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maximum price filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=500.00)
     *     ),
     *     @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         description="Filter by brand",
     *         required=false,
     *         @OA\Schema(type="string", example="3M")
     *     ),
     *     @OA\Parameter(
     *         name="gender",
     *         in="query",
     *         description="Filter by gender",
     *         required=false,
     *         @OA\Schema(type="string", enum={"male", "female", "unisex"}, example="unisex")
     *     ),
     *     @OA\Parameter(
     *         name="in_stock",
     *         in="query",
     *         description="Filter only products in stock",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Filter only featured products",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "price", "created_at", "popularity"}, example="name")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=20)
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         description="Currency for price display",
     *         required=false,
     *         @OA\Schema(type="string", enum={"TRY", "USD", "EUR"}, example="TRY")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
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
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
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
     *     tags={"Products"},
     *     summary="Get single product details",
     *     description="Returns detailed information about a specific product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         description="Currency for price display",
     *         required=false,
     *         @OA\Schema(type="string", enum={"TRY", "USD", "EUR"}, example="TRY")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ProductDetail")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
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

        return new ProductDetailResource($product);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/search-suggestions",
     *     operationId="getProductSuggestions",
     *     tags={"Products"},
     *     summary="Get search suggestions",
     *     description="Returns search suggestions based on partial input",
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query (minimum 2 characters)",
     *         required=true,
     *         @OA\Schema(type="string", minLength=2, example="güv")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maximum number of suggestions",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=20, example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
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
     *     tags={"Products"},
     *     summary="Get available filters",
     *     description="Returns all available filter options for products",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
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