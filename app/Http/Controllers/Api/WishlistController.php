<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Wishlist", description="Kullanıcı istek listesi yönetimi API uç noktaları")
 */
class WishlistController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/wishlist",
     *     summary="Kullanıcının istek listesini al",
     *     description="Kimliği doğrulanmış kullanıcının istek listesindeki tüm öğeleri alın",
     *     operationId="getWishlist",
     *     tags={"Wishlist"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority (1=Low, 2=Medium, 3=High, 4=Urgent)",
     *         required=false,
     *         @OA\Schema(type="integer", enum={1, 2, 3, 4})
     *     ),
     *     @OA\Parameter(
     *         name="favorites_only",
     *         in="query",
     *         description="Show only favorite items",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="available_only",
     *         in="query",
     *         description="Show only items that are in stock",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="İstek listesi başarıyla alındı",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/WishlistResource")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total_items", type="integer", example=15),
     *                 @OA\Property(property="favorite_items", type="integer", example=5),
     *                 @OA\Property(property="high_priority_items", type="integer", example=3)
     *             ),
     *             @OA\Property(property="message", type="string", example="İstek listesi başarıyla alındı")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $query = $request->user()->wishlistItems()
                          ->with(['product.categories', 'productVariant']);

        // Apply filters
        if ($request->filled('priority')) {
            $query->byPriority((int) $request->priority);
        }

        if ($request->boolean('favorites_only')) {
            $query->favorites();
        }

        if ($request->boolean('available_only')) {
            // This requires a more complex query to check stock
            $query->whereHas('product', function ($q) {
                $q->whereHas('variants', function ($vq) {
                    $vq->where('stock', '>', 0);
                });
            });
        }

        $wishlistItems = $query->orderBy('is_favorite', 'desc')
                              ->orderBy('priority', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->get();

        // Calculate metadata
        $totalItems = $request->user()->wishlistItems()->count();
        $favoriteItems = $request->user()->favoriteWishlistItems()->count();
        $highPriorityItems = $request->user()->wishlistItems()->highPriority()->count();

        return response()->json([
            'data' => WishlistResource::collection($wishlistItems),
            'meta' => [
                'total_items' => $totalItems,
                'favorite_items' => $favoriteItems,
                'high_priority_items' => $highPriorityItems,
            ],
            'message' => 'İstek listesi başarıyla alındı'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/wishlist",
     *     summary="İstek listesine öğe ekle",
     *     description="Kullanıcının istek listesine bir ürün veya ürün çeşidi ekleyin",
     *     operationId="addToWishlist",
     *     tags={"Wishlist"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="product_variant_id", type="integer", nullable=true, example=5),
     *             @OA\Property(property="notes", type="string", maxLength=1000, nullable=true, example="Need this for the office"),
     *             @OA\Property(property="priority", type="integer", enum={1, 2, 3, 4}, example=2, description="1=Low, 2=Medium, 3=High, 4=Urgent"),
     *             @OA\Property(property="is_favorite", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Öğe istek listesine başarıyla eklendi",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/WishlistResource"),
     *             @OA\Property(property="message", type="string", example="Öğe istek listesine başarıyla eklendi")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError"),
     *     @OA\Response(
     *         response=409,
     *         description="Item already exists in wishlist",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bu öğe zaten istek listenizde bulunuyor")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'priority' => ['integer', Rule::in([1, 2, 3, 4])],
            'is_favorite' => ['boolean'],
        ]);

        $user = $request->user();

        // Check if item already exists in wishlist
        if (Wishlist::existsForUser(
            $user->id,
            $validated['product_id'],
            $validated['product_variant_id'] ?? null
        )) {
            return response()->json([
                'message' => 'Bu öğe zaten istek listenizde bulunuyor'
            ], 409);
        }

        // Validate product variant belongs to product
        if (isset($validated['product_variant_id'])) {
            $variant = ProductVariant::find($validated['product_variant_id']);
            if ($variant->product_id !== $validated['product_id']) {
                return response()->json([
                    'message' => 'Ürün çeşidi belirtilen ürüne ait değil'
                ], 422);
            }
        }

        $wishlistItem = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $validated['product_id'],
            'product_variant_id' => $validated['product_variant_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'priority' => $validated['priority'] ?? Wishlist::PRIORITY_MEDIUM,
            'is_favorite' => $validated['is_favorite'] ?? false,
            'added_at' => now(),
        ]);

        $wishlistItem->load(['product.categories', 'productVariant']);

        return response()->json([
            'data' => new WishlistResource($wishlistItem),
            'message' => 'Öğe istek listesine başarıyla eklendi'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/wishlist/{id}",
     *     summary="Belirli istek listesi öğesini al",
     *     description="Kullanıcının istek listesinden belirli bir öğeyi alın",
     *     operationId="getWishlistItem",
     *     tags={"Wishlist"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Wishlist item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wishlist item retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/WishlistResource"),
     *             @OA\Property(property="message", type="string", example="Wishlist item retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Request $request, Wishlist $wishlist)
    {
        // Ensure user can only access their own wishlist items
        if ($wishlist->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Wishlist item not found'
            ], 404);
        }

        $wishlist->load(['product.categories', 'productVariant']);

        return response()->json([
            'data' => new WishlistResource($wishlist),
            'message' => 'Wishlist item retrieved successfully'
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/wishlist/{id}",
     *     summary="İstek listesi öğesini güncelle",
     *     description="Kullanıcının istek listesindeki belirli bir öğeyi güncelleyin",
     *     operationId="updateWishlistItem",
     *     tags={"Wishlist"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Wishlist item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="notes", type="string", maxLength=1000, nullable=true, example="Updated notes"),
     *             @OA\Property(property="priority", type="integer", enum={1, 2, 3, 4}, example=3),
     *             @OA\Property(property="is_favorite", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wishlist item updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/WishlistResource"),
     *             @OA\Property(property="message", type="string", example="Wishlist item updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        // Ensure user can only update their own wishlist items
        if ($wishlist->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Wishlist item not found'
            ], 404);
        }

        $validated = $request->validate([
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'priority' => ['sometimes', 'integer', Rule::in([1, 2, 3, 4])],
            'is_favorite' => ['sometimes', 'boolean'],
        ]);

        $wishlist->update($validated);
        $wishlist->load(['product.categories', 'productVariant']);

        return response()->json([
            'data' => new WishlistResource($wishlist),
            'message' => 'Wishlist item updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/wishlist/{id}",
     *     summary="Öğeyi istek listesinden kaldır",
     *     description="Kullanıcının istek listesinden belirli bir öğeyi kaldırın",
     *     operationId="removeFromWishlist",
     *     tags={"Wishlist"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Wishlist item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removed from wishlist successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item removed from wishlist successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Request $request, Wishlist $wishlist)
    {
        // Ensure user can only delete their own wishlist items
        if ($wishlist->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Wishlist item not found'
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'message' => 'Item removed from wishlist successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/wishlist/{id}/toggle-favorite",
     *     summary="Favori durumunu değiştir",
     *     description="Bir istek listesi öğesinin favori durumunu değiştirin",
     *     operationId="toggleWishlistFavorite",
     *     tags={"Wishlist"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Wishlist item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite status toggled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/WishlistResource"),
     *             @OA\Property(property="message", type="string", example="Favorite status updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function toggleFavorite(Request $request, Wishlist $wishlist)
    {
        // Ensure user can only modify their own wishlist items
        if ($wishlist->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Wishlist item not found'
            ], 404);
        }

        $wishlist->toggleFavorite();
        $wishlist->load(['product.categories', 'productVariant']);

        return response()->json([
            'data' => new WishlistResource($wishlist),
            'message' => 'Favorite status updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/wishlist/clear",
     *     summary="Tüm istek listesini temizle",
     *     description="Kullanıcının istek listesindeki tüm öğeleri kaldırın",
     *     operationId="clearWishlist",
     *     tags={"Wishlist"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Wishlist cleared successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Wishlist cleared successfully"),
     *             @OA\Property(property="removed_count", type="integer", example=12)
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function clear(Request $request)
    {
        $user = $request->user();
        $removedCount = $user->wishlistItems()->count();
        $user->wishlistItems()->delete();

        return response()->json([
            'message' => 'Wishlist cleared successfully',
            'removed_count' => $removedCount
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/wishlist/stats",
     *     summary="İstek listesi istatistiklerini al",
     *     description="Kullanıcının istek listesi hakkında istatistikleri alın",
     *     operationId="getWishlistStats",
     *     tags={"Wishlist"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Wishlist statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_items", type="integer", example=25),
     *                 @OA\Property(property="favorite_items", type="integer", example=8),
     *                 @OA\Property(property="high_priority_items", type="integer", example=5),
     *                 @OA\Property(property="available_items", type="integer", example=20),
     *                 @OA\Property(property="priority_breakdown", type="object",
     *                     @OA\Property(property="low", type="integer", example=5),
     *                     @OA\Property(property="medium", type="integer", example=15),
     *                     @OA\Property(property="high", type="integer", example=4),
     *                     @OA\Property(property="urgent", type="integer", example=1)
     *                 ),
     *                 @OA\Property(property="total_value", type="number", format="float", example=2450.75),
     *                 @OA\Property(property="oldest_item_date", type="string", format="datetime"),
     *                 @OA\Property(property="newest_item_date", type="string", format="datetime")
     *             ),
     *             @OA\Property(property="message", type="string", example="Wishlist statistics retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        
        $totalItems = $user->wishlistItems()->count();
        $favoriteItems = $user->favoriteWishlistItems()->count();
        $highPriorityItems = $user->wishlistItems()->highPriority()->count();
        
        // Calculate available items (this is a simplified version)
        $availableItems = $user->wishlistItems()
            ->whereHas('product', function ($q) {
                $q->whereHas('variants', function ($vq) {
                    $vq->where('stock', '>', 0);
                });
            })
            ->count();

        // Priority breakdown
        $priorityBreakdown = [
            'low' => $user->wishlistItems()->byPriority(Wishlist::PRIORITY_LOW)->count(),
            'medium' => $user->wishlistItems()->byPriority(Wishlist::PRIORITY_MEDIUM)->count(),
            'high' => $user->wishlistItems()->byPriority(Wishlist::PRIORITY_HIGH)->count(),
            'urgent' => $user->wishlistItems()->byPriority(Wishlist::PRIORITY_URGENT)->count(),
        ];

        // Calculate total value
        $wishlistItems = $user->wishlistItems()->with(['product', 'productVariant'])->get();
        $totalValue = $wishlistItems->sum(function ($item) {
            return $item->getCurrentPrice() ?? 0;
        });

        // Date ranges
        $oldestItem = $user->wishlistItems()->oldest()->first();
        $newestItem = $user->wishlistItems()->latest()->first();

        return response()->json([
            'data' => [
                'total_items' => $totalItems,
                'favorite_items' => $favoriteItems,
                'high_priority_items' => $highPriorityItems,
                'available_items' => $availableItems,
                'priority_breakdown' => $priorityBreakdown,
                'total_value' => round($totalValue, 2),
                'oldest_item_date' => $oldestItem?->created_at->toISOString(),
                'newest_item_date' => $newestItem?->created_at->toISOString(),
            ],
            'message' => 'Wishlist statistics retrieved successfully'
        ]);
    }
}