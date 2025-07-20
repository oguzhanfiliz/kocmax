<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test With Backup Product Model ===\n";

try {
    $product = \App\Models\ProductBackup::select('id', 'name')->first();
    echo "Success: ProductBackup loaded - " . $product->name . "\n";
    
    // Test model instantiation
    $newProduct = new \App\Models\ProductBackup();
    echo "Success: New ProductBackup instance created\n";
    
    // Test scopes
    $activeProducts = \App\Models\ProductBackup::active()->count();
    echo "Success: Active products count: $activeProducts\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "Test completed\n";