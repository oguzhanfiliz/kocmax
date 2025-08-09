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
            // Add new columns for simplified product structure
            $table->text('short_description')->nullable()->after('description');
            
            // Add new columns
            $table->string('brand')->nullable()->after('price');
            $table->string('model')->nullable()->after('brand');
            $table->string('material')->nullable()->after('model');
            $table->enum('gender', ['unisex', 'male', 'female', 'kids'])->default('unisex')->after('material');
            $table->string('safety_standard')->nullable()->after('gender');
        });
        
        // Rename price to base_price in separate statement
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'price')) {
                $table->renameColumn('price', 'base_price');
            }
        });
        
        // NOTE: İstediğiniz üzere ürün tablosundaki mevcut kolonları (cost, stock, min_stock_level,
        // discounted_price, views, dimensions) şu an için KORUYORUZ. Gelecekte kaldırma kararı alınırsa,
        // ayrı bir temizlik migrasyonunda ele alınacaktır.

        // Update product_variants table structure
        Schema::table('product_variants', function (Blueprint $table) {
            // Add new columns
            $table->string('barcode')->nullable()->after('sku');
            $table->decimal('cost', 10, 2)->nullable()->after('price');
            $table->integer('min_stock_level')->default(0)->after('stock');
            $table->string('color')->nullable()->after('min_stock_level');
            $table->string('size')->nullable()->after('color');
            $table->decimal('weight', 8, 3)->nullable()->after('size');
            $table->json('dimensions')->nullable()->after('weight');
            $table->boolean('is_default')->default(false)->after('is_active');
            $table->integer('sort_order')->default(0)->after('is_default');
            $table->string('image_url')->nullable()->after('sort_order');
            
            // Remove attributes column if it exists
            if (Schema::hasColumn('product_variants', 'attributes')) {
                $table->dropColumn('attributes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back removed columns first
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->integer('views')->default(0);
            $table->json('dimensions')->nullable();
        });
        
        // Rename back to price
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'base_price')) {
                $table->renameColumn('base_price', 'price');
            }
        });
        
        // Remove added columns
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'short_description',
                'brand',
                'model', 
                'material',
                'gender',
                'safety_standard'
            ]);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'barcode',
                'cost',
                'min_stock_level',
                'color',
                'size',
                'weight',
                'dimensions',
                'is_default',
                'sort_order',
                'image_url'
            ]);
            
            // Add back attributes column
            $table->json('attributes')->nullable();
        });
    }
};