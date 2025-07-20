<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;

class ProductCacheService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get product with cached variants count
     */
    public function getProductWithVariantsCount(int $productId): ?array
    {
        $cacheKey = "product_variants_count_{$productId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($productId) {
            $product = Product::find($productId);
            
            if (!$product) {
                return null;
            }
            
            return [
                'product' => $product,
                'variants_count' => $product->variants()->count(),
                'active_variants_count' => $product->variants()->active()->count(),
                'in_stock_count' => $product->variants()->inStock()->count(),
            ];
        });
    }

    /**
     * Get product price range
     */
    public function getProductPriceRange(int $productId): array
    {
        $cacheKey = "product_price_range_{$productId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($productId) {
            $prices = ProductVariant::where('product_id', $productId)
                ->active()
                ->pluck('price');
            
            if ($prices->isEmpty()) {
                $basePrice = Product::find($productId)?->base_price ?? 0;
                return [
                    'min' => $basePrice,
                    'max' => $basePrice,
                    'range' => number_format($basePrice, 2) . ' ₺',
                ];
            }
            
            $min = $prices->min();
            $max = $prices->max();
            
            return [
                'min' => $min,
                'max' => $max,
                'range' => $min == $max 
                    ? number_format($min, 2) . ' ₺'
                    : number_format($min, 2) . ' - ' . number_format($max, 2) . ' ₺',
            ];
        });
    }

    /**
     * Get product available options
     */
    public function getProductAvailableOptions(int $productId): array
    {
        $cacheKey = "product_available_options_{$productId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($productId) {
            $variants = ProductVariant::with('variantOptions.variantType')
                ->where('product_id', $productId)
                ->active()
                ->inStock()
                ->get();
            
            $options = [];
            
            foreach ($variants as $variant) {
                foreach ($variant->variantOptions as $option) {
                    $typeSlug = $option->variantType->slug;
                    
                    if (!isset($options[$typeSlug])) {
                        $options[$typeSlug] = [
                            'type' => $option->variantType->display_name,
                            'options' => [],
                        ];
                    }
                    
                    $options[$typeSlug]['options'][$option->id] = [
                        'id' => $option->id,
                        'name' => $option->display_value,
                        'hex_color' => $option->hex_color,
                        'image_url' => $option->image_url,
                    ];
                }
            }
            
            return $options;
        });
    }

    /**
     * Clear product cache
     */
    public function clearProductCache(int $productId): void
    {
        Cache::forget("product_variants_count_{$productId}");
        Cache::forget("product_price_range_{$productId}");
        Cache::forget("product_available_options_{$productId}");
        Cache::forget("product_total_stock_{$productId}");
    }

    /**
     * Get optimized products list for admin panel
     */
    public static function getOptimizedProductsList(int $page = 1, int $perPage = 25): array
    {
        $cacheKey = "admin_products_list_page_{$page}_per_{$perPage}";
        
        return Cache::remember($cacheKey, 900, function () use ($perPage) { // 15 min cache
            return Product::select([
                'id',
                'name',
                'slug', 
                'sku',
                'base_price',
                'brand',
                'brand_id',
                'is_active',
                'is_featured',
                'created_at',
                'sort_order'
            ])
            ->with(['brand:id,name'])
            ->withCount(['variants'])
            ->orderBy('sort_order', 'asc')
            ->paginate($perPage)
            ->toArray();
        });
    }

    /**
     * Clear all product caches
     */
    public function clearAllProductCaches(): void
    {
        // Clear category cache
        Cache::forget('categories_tree_select');
        
        // Clear admin product lists
        for ($page = 1; $page <= 20; $page++) {
            foreach ([10, 25, 50, 100] as $perPage) {
                Cache::forget("admin_products_list_page_{$page}_per_{$perPage}");
            }
        }
        
        // Use cache tags if available
        if (Cache::supportsTags()) {
            Cache::tags(['products'])->flush();
        } else {
            // Manual clearing - limit to prevent memory issues
            $products = Product::select('id')->limit(1000)->pluck('id');
            foreach ($products as $productId) {
                $this->clearProductCache($productId);
            }
        }
    }
}
