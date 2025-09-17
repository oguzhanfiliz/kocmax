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
            if (!Schema::hasColumn('cart_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable()->after('calculated_price');
            }

            if (!Schema::hasColumn('cart_items', 'unit_tax_amount')) {
                $table->decimal('unit_tax_amount', 12, 2)->default(0)->after('tax_rate');
            }

            if (!Schema::hasColumn('cart_items', 'total_tax_amount')) {
                $table->decimal('total_tax_amount', 12, 2)->default(0)->after('unit_tax_amount');
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'tax_amount')) {
                $table->decimal('tax_amount', 12, 2)->default(0)->after('subtotal_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'total_tax_amount')) {
                $table->dropColumn('total_tax_amount');
            }

            if (Schema::hasColumn('cart_items', 'unit_tax_amount')) {
                $table->dropColumn('unit_tax_amount');
            }

            if (Schema::hasColumn('cart_items', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }
        });
    }
};
