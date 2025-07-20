<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Minimal Memory Test ===\n";

// Test 1: Raw DB queries only
$productCount = \DB::table('products')->count();
echo "Products: $productCount\n";

$categoryCount = \DB::table('categories')->count();
echo "Categories: $categoryCount\n";

$pivotCount = \DB::table('product_categories')->count();
echo "Pivot: $pivotCount\n";

// Test 2: Check if observers are causing the issue
try {
    // Remove ALL observers from Product model
    \App\Models\Product::flushEventListeners();
    \App\Models\Category::flushEventListeners();
    \App\Models\ProductVariant::flushEventListeners();
    
    echo "Observers flushed\n";
    
    // Try to load ONE product
    $product = \App\Models\Product::select('id', 'name')->first();
    echo "Product loaded: " . $product->name . "\n";
    
} catch (\Exception $e) {
    echo "HATA Model: " . $e->getMessage() . "\n";
}

echo "Test completed\n";