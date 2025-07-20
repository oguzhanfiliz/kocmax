<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function memoryUsage($step) {
    $usage = memory_get_usage(true) / 1024 / 1024;
    echo sprintf("%s: %.2f MB\n", $step, $usage);
}

echo "=== Debug Memory Issue ===\n";
memoryUsage('1. Başlangıç');

// Test raw queries first
$productCount = \DB::table('products')->count();
echo "Products count: $productCount\n";
memoryUsage('2. Product count');

$categoryCount = \DB::table('categories')->count();
echo "Categories count: $categoryCount\n";
memoryUsage('3. Category count');

$pivotCount = \DB::table('product_categories')->count();
echo "Product-Category pivot count: $pivotCount\n";
memoryUsage('4. Pivot count');

// Test with disabled observers
\App\Models\Product::unsetEventDispatcher();
\App\Models\Category::unsetEventDispatcher();

$product = \App\Models\Product::first();
memoryUsage('5. First product without events');

// Test category loading step by step
try {
    $categoryIds = \DB::table('product_categories')
        ->where('product_id', $product->id)
        ->pluck('category_id');
    echo "Category IDs for product: " . implode(', ', $categoryIds->toArray()) . "\n";
    memoryUsage('6. Category IDs');
} catch (\Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}

// Test raw category query
try {
    $categories = \DB::table('categories')
        ->whereIn('id', $categoryIds)
        ->get();
    echo "Categories found: " . count($categories) . "\n";
    memoryUsage('7. Raw categories');
} catch (\Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}