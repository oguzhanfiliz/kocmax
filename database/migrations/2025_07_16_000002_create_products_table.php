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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable()->index();
            $table->text('description')->nullable();
            $table->string('sku')->unique()->index();
            $table->string('barcode')->nullable()->index();
            $table->decimal('price', 12, 2);
            $table->decimal('discounted_price', 12, 2)->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->integer('stock')->default(0)->index();
            $table->integer('min_stock_level')->default(5);
            $table->integer('views')->default(0);
            $table->decimal('weight', 8, 3)->nullable();
            $table->json('dimensions')->nullable(); // {length, width, height}
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_new')->default(false)->index();
            $table->boolean('is_bestseller')->default(false)->index();
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'is_featured', 'sort_order']);
            $table->index(['stock', 'min_stock_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
