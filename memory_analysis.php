<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function memoryUsage($step) {
    $usage = memory_get_usage(true) / 1024 / 1024;
    $peak = memory_get_peak_usage(true) / 1024 / 1024;
    echo sprintf("%s: Current: %.2f MB, Peak: %.2f MB\n", $step, $usage, $peak);
}

echo "=== Memory Analysis Started ===\n";
memoryUsage('1. Başlangıç');

// Test basic model loading
echo "\n=== Test 1: Basic Product Model ===\n";
$product = new \App\Models\Product();
memoryUsage('2. Product model oluşturuldu');

// Test count query
$count = \App\Models\Product::count();
memoryUsage('3. Product::count() - ' . $count . ' ürün');

// Test simple query
$products = \App\Models\Product::take(5)->get();
memoryUsage('4. İlk 5 ürün getirildi');

// Test with relationships
echo "\n=== Test 2: With Relationships ===\n";
try {
    $productsWithCategories = \App\Models\Product::with('categories')->take(1)->get();
    memoryUsage('5. 1 ürün + kategoriler');
} catch (\Exception $e) {
    echo "HATA: Kategori ilişkisi - " . $e->getMessage() . "\n";
    memoryUsage('5. Kategori hatası');
}

try {
    $productsWithVariants = \App\Models\Product::with('variants')->take(1)->get();
    memoryUsage('6. 1 ürün + varyantlar');
} catch (\Exception $e) {
    echo "HATA: Varyant ilişkisi - " . $e->getMessage() . "\n";
    memoryUsage('6. Varyant hatası');
}

// Test complex relationships
echo "\n=== Test 3: Complex Relationships ===\n";
$productsWithAll = \App\Models\Product::with(['categories', 'variants', 'primaryImage'])->take(5)->get();
memoryUsage('7. 5 ürün + tüm ilişkiler');

// Test category relationships
echo "\n=== Test 4: Category Analysis ===\n";
$categories = \App\Models\Category::count();
memoryUsage('8. Category::count() - ' . $categories . ' kategori');

$categoriesWithProducts = \App\Models\Category::with('products')->take(5)->get();
memoryUsage('9. 5 kategori + ürünler');

// Test variant analysis
echo "\n=== Test 5: Variant Analysis ===\n";
$variants = \App\Models\ProductVariant::count();
memoryUsage('10. ProductVariant::count() - ' . $variants . ' varyant');

$variantsWithProducts = \App\Models\ProductVariant::with('product')->take(10)->get();
memoryUsage('11. 10 varyant + ürünler');

// Test problematic queries
echo "\n=== Test 6: Problematic Queries ===\n";
$allProducts = \App\Models\Product::all();
memoryUsage('12. Product::all() - TÜM ÜRÜNLER');

echo "\n=== Memory Analysis Complete ===\n";