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
        Schema::table('carts', function (Blueprint $table) {
            // Pricing integration fields
            $table->timestamp('pricing_calculated_at')->nullable()->after('coupon_discount');
            $table->timestamp('last_pricing_update')->nullable()->after('pricing_calculated_at');
            $table->json('pricing_context')->nullable()->after('last_pricing_update');
            
            // Additional cart metadata
            $table->string('customer_type', 20)->nullable()->after('pricing_context');
            $table->decimal('subtotal_amount', 10, 2)->default(0)->after('customer_type');
            $table->json('applied_discounts')->nullable()->after('subtotal_amount');
            
            // Indexes for performance
            $table->index(['user_id', 'pricing_calculated_at']);
            $table->index(['session_id', 'last_pricing_update']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'pricing_calculated_at']);
            $table->dropIndex(['session_id', 'last_pricing_update']);
            
            $table->dropColumn([
                'pricing_calculated_at',
                'last_pricing_update', 
                'pricing_context',
                'customer_type',
                'subtotal_amount',
                'applied_discounts'
            ]);
        });
    }
};
