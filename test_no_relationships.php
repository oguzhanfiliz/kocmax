<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test Without Relationships ===\n";

// Test: Create a simple Product model without relationships
class SimpleProduct extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'products';
    
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'base_price',
        'is_active',
    ];
    
    // NO RELATIONSHIPS AT ALL
}

try {
    $product = SimpleProduct::select('id', 'name')->first();
    echo "Success: SimpleProduct loaded - " . $product->name . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "Test completed\n";