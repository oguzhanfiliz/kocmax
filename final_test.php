<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function memoryUsage($step) {
    $usage = memory_get_usage(true) / 1024 / 1024;
    echo sprintf("%s: %.2f MB\n", $step, $usage);
}

echo "=== Final Memory Test ===\n";
memoryUsage('1. Başlangıç');

// Test 1: Basic Product loading
$product = \App\Models\Product::select('id', 'name', 'sku')->first();
echo "Product loaded: " . $product->name . "\n";
memoryUsage('2. Product yüklendi');

// Test 2: Category loading
$category = \App\Models\Category::select('id', 'name')->first();
echo "Category loaded: " . $category->name . "\n";
memoryUsage('3. Category yüklendi');

// Test 3: Product variants (if any)
try {
    $variants = $product->variants;
    echo "Product variants: " . $variants->count() . "\n";
    memoryUsage('4. Variants yüklendi');
} catch (\Exception $e) {
    echo "HATA Variants: " . $e->getMessage() . "\n";
}

// Test 4: Product categories (this was the problem)
try {
    $categories = $product->categories;
    echo "Product categories: " . $categories->count() . "\n";
    memoryUsage('5. Categories yüklendi');
} catch (\Exception $e) {
    echo "HATA Categories: " . $e->getMessage() . "\n";
}

// Test 5: Multiple products
try {
    $products = \App\Models\Product::select('id', 'name', 'sku')->take(5)->get();
    echo "5 products loaded\n";
    memoryUsage('6. 5 ürün yüklendi');
} catch (\Exception $e) {
    echo "HATA Multiple: " . $e->getMessage() . "\n";
}

echo "\n=== Test tamamlandı ===\n";