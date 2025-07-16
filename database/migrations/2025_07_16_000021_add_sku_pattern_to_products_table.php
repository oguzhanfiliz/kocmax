<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Make SKU nullable for auto-generation
            $table->string('sku')->nullable()->change();
        });

        // Create SKU configuration table
        Schema::create('sku_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('pattern'); // e.g., {CATEGORY}-{PRODUCT}-{NUMBER}
            $table->string('separator')->default('-');
            $table->integer('number_length')->default(3);
            $table->integer('last_number')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable(false)->change();
        });
        
        Schema::dropIfExists('sku_configurations');
    }
};
