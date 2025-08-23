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
        Schema::table('campaigns', function (Blueprint $table) {
            // X Al Y Hediye Kampanya Alanları
            $table->integer('required_quantity')->nullable()->after('minimum_cart_amount');
            $table->integer('free_quantity')->nullable()->after('required_quantity');
            
            // Paket İndirim Kampanya Alanları
            $table->string('bundle_discount_type')->nullable()->after('free_quantity');
            $table->decimal('bundle_discount_value', 12, 2)->nullable()->after('bundle_discount_type');
            
            // Ücretsiz Kargo Kampanya Alanları
            $table->decimal('free_shipping_min_amount', 12, 2)->nullable()->after('bundle_discount_value');
            
            // Flaş İndirim Kampanya Alanları
            $table->string('flash_discount_type')->nullable()->after('free_shipping_min_amount');
            $table->decimal('flash_discount_value', 12, 2)->nullable()->after('flash_discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'required_quantity',
                'free_quantity',
                'bundle_discount_type',
                'bundle_discount_value',
                'free_shipping_min_amount',
                'flash_discount_type',
                'flash_discount_value'
            ]);
        });
    }
};
