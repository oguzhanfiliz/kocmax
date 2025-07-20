<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function memoryUsage($step) {
    $usage = memory_get_usage(true) / 1024 / 1024;
    echo sprintf("%s: %.2f MB\n", $step, $usage);
}

echo "=== Simple Category Test ===\n";
memoryUsage('1. Başlangıç');

// Test direct category query
$categories = \App\Models\Category::take(1)->get();
memoryUsage('2. 1 kategori getirildi');

// Test category with products
try {
    $category = \App\Models\Category::first();
    memoryUsage('3. İlk kategori');
    
    $products = $category->products()->take(1)->get();
    memoryUsage('4. Kategorinin 1 ürünü');
    
} catch (\Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}

echo "\n=== Simple Product Test ===\n";

// Test product without relationships
$product = \App\Models\Product::first();
memoryUsage('5. İlk ürün');

// Test product categories relationship
try {
    $productCategories = $product->categories;
    memoryUsage('6. Ürünün kategorileri');
} catch (\Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}

echo "\n=== Database Stats ===\n";
echo "Products: " . \App\Models\Product::count() . "\n";
echo "Categories: " . \App\Models\Category::count() . "\n";
echo "Product-Category relations: " . \DB::table('product_categories')->count() . "\n";