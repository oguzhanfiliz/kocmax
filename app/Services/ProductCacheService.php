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
     * Clear all product caches
     */
    public function clearAllProductCaches(): void
    {
        // Use cache tags if available
        if (Cache::supportsTags()) {
            Cache::tags(['products'])->flush();
        } else {
            // Manual clearing for drivers that don't support tags
            $products = Product::pluck('id');
            foreach ($products as $productId) {
                $this->clearProductCache($productId);
            }
        }
    }
}
