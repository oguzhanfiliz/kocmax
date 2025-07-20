<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;

class ProductVariantObserver
{
    /**
     * Handle the ProductVariant "created" event.
     */
    public function created(ProductVariant $productVariant): void
    {
        $this->clearProductCache($productVariant);
    }

    /**
     * Handle the ProductVariant "updated" event.
     */
    public function updated(ProductVariant $productVariant): void
    {
        $this->clearProductCache($productVariant);
        $this->clearVariantCache($productVariant);
    }

    /**
     * Handle the ProductVariant "deleted" event.
     */
    public function deleted(ProductVariant $productVariant): void
    {
        $this->clearProductCache($productVariant);
        $this->clearVariantCache($productVariant);
    }

    /**
     * Clear product related cache
     */
    private function clearProductCache(ProductVariant $productVariant): void
    {
        $productId = $productVariant->product_id;
        
        $cacheKeys = [
            "product_total_stock_{$productId}",
            "product_min_price_{$productId}",
            "product_max_price_{$productId}",
            "product_colors_{$productId}",
            "product_sizes_{$productId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear variant related cache
     */
    private function clearVariantCache(ProductVariant $productVariant): void
    {
        Cache::forget("variant_display_name_{$productVariant->id}");
    }
}