<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\PopularSearch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Search", description="Arama API uç noktaları")
 */
class SearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/search/popular",
     *     summary="Popüler arama terimlerini getir",
     *     description="En çok aranan terimleri döndürür",
     *     operationId="getPopularSearches",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Döndürülecek maksimum sonuç sayısı",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=50, default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Popüler aramalar başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Popüler aramalar getirildi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="string", example="güvenlik ayakkabısı")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->integer('limit', 10);
            $limit = max(1, min(50, $limit)); // 1-50 arası sınırla

            $searches = PopularSearch::getPopular($limit);

            return response()->json([
                'success' => true,
                'message' => 'Popüler aramalar getirildi',
                'data' => $searches
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Popüler aramalar getirilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/search/autocomplete",
     *     summary="Arama önerilerini getir",
     *     description="Girilen metne göre ürün, kategori ve arama önerilerini döndürür",
     *     operationId="getAutocomplete",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Arama terimi (minimum 2 karakter)",
     *         required=true,
     *         @OA\Schema(type="string", minLength=2, example="güvenlik")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Kategori başına maksimum sonuç sayısı",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=20, default=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Arama önerileri başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Arama önerileri getirildi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="slug", type="string"),
     *                         @OA\Property(property="base_price", type="number")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="slug", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="suggestions",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Geçersiz arama terimi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Arama terimi en az 2 karakter olmalıdır")
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function autocomplete(Request $request): JsonResponse
    {
        try {
            $query = $request->string('q', '')->toString();
            $query = trim($query);
            $limit = $request->integer('limit', 5);
            $limit = max(1, min(20, $limit)); // 1-20 arası sınırla

            // Minimum 2 karakter kontrolü
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arama terimi en az 2 karakter olmalıdır'
                ], 400);
            }

            // Arama terimini kaydet (sync olarak)
            PopularSearch::recordSearch($query);

            // Ürünlerde ara
            $products = Product::active()
                ->search($query)
                ->with(['categories'])
                ->limit($limit)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'base_price' => $product->base_price,
                    ];
                });

            // Kategorilerde ara
            $categories = Category::where('name', 'LIKE', "%{$query}%")
                ->where('is_active', true)
                ->limit($limit)
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ];
                });

            // Arama önerilerini getir
            $suggestions = PopularSearch::getSuggestions($query, $limit);

            return response()->json([
                'success' => true,
                'message' => 'Arama önerileri getirildi',
                'data' => [
                    'products' => $products,
                    'categories' => $categories,
                    'suggestions' => $suggestions,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama önerileri getirilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/search/record",
     *     summary="Arama terimini kaydet",
     *     description="Kullanıcının arama terimini kaydeder (popüler aramalar için)",
     *     operationId="recordSearch",
     *     tags={"Search"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="query", type="string", minLength=2, example="güvenlik ayakkabısı")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Arama terimi başarıyla kaydedildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Arama terimi kaydedildi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Geçersiz arama terimi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Arama terimi en az 2 karakter olmalıdır")
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function record(Request $request): JsonResponse
    {
        try {
            $query = $request->string('query', '')->toString();
            $query = trim($query);

            // Minimum 2 karakter kontrolü
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arama terimi en az 2 karakter olmalıdır'
                ], 400);
            }

            PopularSearch::recordSearch($query);

            return response()->json([
                'success' => true,
                'message' => 'Arama terimi kaydedildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama terimi kaydedilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}