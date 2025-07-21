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
        Schema::table('users', function (Blueprint $table) {
            // Customer pricing tier
            $table->foreignId('pricing_tier_id')->nullable()->after('email_verified_at')->constrained('customer_pricing_tiers')->nullOnDelete();
            
            // Customer type override
            $table->enum('customer_type_override', ['b2b', 'b2c', 'wholesale', 'retail'])->nullable()->after('pricing_tier_id');
            
            // Pricing preferences
            $table->decimal('custom_discount_percentage', 5, 2)->nullable()->after('customer_type_override');
            $table->boolean('allow_backorders')->default(false)->after('custom_discount_percentage');
            $table->integer('payment_terms_days')->nullable()->after('allow_backorders'); // B2B payment terms
            $table->decimal('credit_limit', 12, 2)->nullable()->after('payment_terms_days'); // B2B credit limit
            
            // Loyalty program
            $table->integer('loyalty_points')->default(0)->after('credit_limit');
            $table->timestamp('last_order_at')->nullable()->after('loyalty_points');
            $table->decimal('lifetime_value', 12, 2)->default(0)->after('last_order_at');
            
            // Add indexes
            $table->index('pricing_tier_id');
            $table->index('customer_type_override');
            $table->index('last_order_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pricing_tier_id']);
            $table->dropIndex(['pricing_tier_id']);
            $table->dropIndex(['customer_type_override']);
            $table->dropIndex(['last_order_at']);
            
            $table->dropColumn([
                'pricing_tier_id',
                'customer_type_override',
                'custom_discount_percentage',
                'allow_backorders',
                'payment_terms_days',
                'credit_limit',
                'loyalty_points',
                'last_order_at',
                'lifetime_value'
            ]);
        });
    }
};