<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test With Clean Product Model ===\n";

try {
    $product = \App\Models\ProductClean::select('id', 'name')->first();
    echo "Success: ProductClean loaded - " . $product->name . "\n";
    
    // Test loading multiple products
    $products = \App\Models\ProductClean::take(5)->get();
    echo "Success: 5 products loaded\n";
    
    // Test with all fields
    $productFull = \App\Models\ProductClean::first();
    echo "Success: Full product loaded - " . $productFull->name . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "Test completed\n";