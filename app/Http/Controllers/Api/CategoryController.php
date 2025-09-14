<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Kategori yönetimi API uç noktaları - Public endpoints (Authentication not required)"
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     operationId="getCategories",
     *     tags={"Categories", "Public API"},
     *     summary="Kategorileri listele (Public)",
     *     description="Aktif kategorileri hiyerarşik yapıda getirir. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="query",
     *         description="Ana kategori ID'si (alt kategorileri getirmek için)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="level",
     *         in="query",
     *         description="Kategori seviyesi (0: ana kategoriler, 1: alt kategoriler)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=0, maximum=3, example=0)
     *     ),
     *     @OA\Parameter(
     *         name="with_products",
     *         in="query",
     *         description="Ürün sayısını dahil et (products_count alanı için)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Sayfa başına kategori sayısı",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategoriler başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kategoriler başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"sanctum":{}}}
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Cache key oluştur - parametrelere göre
        $cacheKey = 'categories.index.' . md5(serialize([
            'parent_id' => $request->input('parent_id'),
            'level' => $request->input('level'),
            'with_products' => $request->boolean('with_products'),
            'per_page' => $request->input('per_page')
        ]));

        // Cache'den veri al (1 saat cache)
        $categories = Cache::remember($cacheKey, 3600, function() use ($request) {
            $query = Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name');

            // Filter by parent category
            if ($request->filled('parent_id')) {
                $query->where('parent_id', $request->input('parent_id'));
            }

            // Filter by level
            if ($request->filled('level')) {
                $level = (int) $request->input('level');
                if ($level === 0) {
                    $query->whereNull('parent_id');
                } else {
                    $query->whereNotNull('parent_id');
                }
            }

            // 🚀 with_products=true parametresi geldiğinde products_count hesapla
            if ($request->boolean('with_products')) {
                $query->withCount(['products' => function($q) {
                    $q->where('is_active', true);
                }]);
            }

            return $query->get();
        });

        return CategoryResource::collection($categories);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/menu",
     *     operationId="getMenuCategories",
     *     tags={"Categories", "Public API"},
     *     summary="Menü kategorilerini getir (Public)",
     *     description="Menüde gösterilmek üzere işaretlenmiş kategorileri hiyerarşik yapıda getirir. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="with_children",
     *         in="query",
     *         description="Alt kategorileri de dahil et",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="include_non_root",
     *         in="query",
     *         description="Kök olmayan (parent_id dolu) menü kategorilerini de düz liste olarak dahil et",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Menü kategorileri başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Menü kategorileri başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     )
     * )
     */
    public function menu(Request $request): JsonResponse
    {
        // Cache key oluştur - parametrelere göre
        $cacheKey = 'categories.menu.' . md5(serialize([
            'with_children' => $request->boolean('with_children', true),
            'include_non_root' => $request->boolean('include_non_root', false)
        ]));

        // Cache'den veri al (2 saat cache - menü sık değişmez)
        $categories = Cache::remember($cacheKey, 7200, function() use ($request) {
            $withChildren = $request->boolean('with_children', true);
            $includeNonRoot = $request->boolean('include_non_root', false);

            $query = Category::query()
                ->where('is_active', true)
                ->where('is_in_menu', true)
                ->orderBy('sort_order')
                ->orderBy('name');

            // Varsayılan: sadece kök kategoriler
            if (! $includeNonRoot) {
                $query->whereNull('parent_id');

                if ($withChildren) {
                    $query->with(['children' => function ($childQuery) {
                        $childQuery->where('is_active', true)
                                  ->where('is_in_menu', true)
                                  ->orderBy('sort_order')
                                  ->orderBy('name');
                    }]);
                }
            } else {
                // Düz liste modunda children yükleme yapma (duplikasyon olmaması için)
                $withChildren = false;
            }

            return $query->get();
        });

        return response()->json([
            'success' => true,
            'message' => 'Menü kategorileri başarıyla getirildi',
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/featured",
     *     operationId="getFeaturedCategories",
     *     tags={"Categories", "Public API"},
     *     summary="Öne çıkarılan kategorileri getir (Public)",
     *     description="Öne çıkarılan olarak işaretlenmiş kategorileri resimlerle birlikte getirir. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maksimum kategori sayısı",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=20, default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Öne çıkarılan kategoriler başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Öne çıkarılan kategoriler başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     )
     * )
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 10), 20);

        $categories = Category::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Öne çıkarılan kategoriler başarıyla getirildi',
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/tree",
     *     operationId="getCategoryTree",
     *     tags={"Categories"},
     *     summary="Kategori ağacını getir",
     *     description="Tüm kategorileri hiyerarşik ağaç yapısında getirir",
     *     @OA\Response(
     *         response=200,
     *         description="Kategori ağacı başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kategori ağacı başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CategoryTree"))
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function tree(): JsonResponse
    {
        $categories = Category::with(['children' => function ($query) {
            $query->where('is_active', true)
                  ->orderBy('sort_order')
                  ->orderBy('name');
        }])
        ->where('is_active', true)
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Kategori ağacı başarıyla getirildi',
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/breadcrumb/{id}",
     *     operationId="getCategoryBreadcrumb",
     *     tags={"Categories"},
     *     summary="Kategori breadcrumb'ını getir",
     *     description="Belirtilen kategori için breadcrumb yolunu getirir",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Kategori ID'si",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Breadcrumb başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Breadcrumb başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CategoryBreadcrumb"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Kategori bulunamadı"),
     *     security={{"sanctum":{}}}
     * )
     */
    public function breadcrumb(int $id): JsonResponse
    {
        $category = Category::where('is_active', true)->findOrFail($id);
        
        $breadcrumb = [];
        $current = $category;
        
        // Build breadcrumb from current category to root
        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        return response()->json([
            'success' => true,
            'message' => 'Breadcrumb başarıyla getirildi',
            'data' => $breadcrumb
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     operationId="getCategory",
     *     tags={"Categories"},
     *     summary="Kategori detayını getir",
     *     description="Belirtilen kategori ID'sine ait detayları getirir",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Kategori ID'si",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategori detayı başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kategori başarıyla getirildi"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Kategori bulunamadı"),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::with(['children', 'parent'])
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla getirildi',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}/products",
     *     operationId="getCategoryProducts",
     *     tags={"Categories"},
     *     summary="Kategoriye ait ürünleri getir",
     *     description="Belirtilen kategori ID'sine ait aktif ürünleri getirir",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Kategori ID'si",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Sayfa başına ürün sayısı",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=50, default=15)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sıralama kriteri",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "price", "created_at"}, default="name")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sıralama yönü",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategori ürünleri başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kategori ürünleri başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Kategori bulunamadı"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function products(Request $request, int $id): AnonymousResourceCollection
    {
        $category = Category::where('is_active', true)->findOrFail($id);
        
        $perPage = min((int) $request->input('per_page', 15), 50);
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // Validate sort parameters
        $allowedSortFields = ['name', 'base_price', 'created_at', 'sort_order'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $products = $category->products()
            ->where('is_active', true)
            ->with(['images', 'variants.variantOptions.variantType'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return ProductResource::collection($products);
    }
}
