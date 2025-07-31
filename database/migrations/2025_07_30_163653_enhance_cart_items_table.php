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
        Schema::table('cart_items', function (Blueprint $table) {
            // Pricing result fields
            $table->decimal('base_price', 10, 2)->nullable()->after('discounted_price');
            $table->decimal('calculated_price', 10, 2)->nullable()->after('base_price');
            $table->json('applied_discounts')->nullable()->after('calculated_price');
            
            // Additional item metadata
            $table->decimal('unit_discount', 10, 2)->default(0)->after('applied_discounts');
            $table->decimal('total_discount', 10, 2)->default(0)->after('unit_discount');
            $table->timestamp('price_calculated_at')->nullable()->after('total_discount');
            
            // Indexes for performance
            $table->index(['cart_id', 'calculated_price']);
            $table->index(['product_variant_id', 'price_calculated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['cart_id', 'calculated_price']);
            $table->dropIndex(['product_variant_id', 'price_calculated_at']);
            
            $table->dropColumn([
                'base_price',
                'calculated_price',
                'applied_discounts',
                'unit_discount',
                'total_discount',
                'price_calculated_at'
            ]);
        });
    }
};
