<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearProductCache($product);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearProductCache($product);
    }

    /**
     * Clear product related cache
     */
    private function clearProductCache(Product $product): void
    {
        $cacheKeys = [
            "product_total_stock_{$product->id}",
            "product_min_price_{$product->id}",
            "product_max_price_{$product->id}",
            "product_colors_{$product->id}",
            "product_sizes_{$product->id}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}