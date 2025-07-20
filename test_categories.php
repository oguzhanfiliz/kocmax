<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test Categories Relationship ===\n";

try {
    $product = \App\Models\ProductClean::first();
    echo "Success: Product loaded - " . $product->name . "\n";
    
    // Test categories relationship
    $categories = $product->categories;
    echo "Success: Categories loaded - " . $categories->count() . " categories\n";
    
    foreach ($categories as $category) {
        echo "- " . $category->name . "\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "Test completed\n";