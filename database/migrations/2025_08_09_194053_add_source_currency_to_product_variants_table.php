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
        Schema::table('product_variants', function (Blueprint $table) {
            // Add source currency fields
            $table->string('source_currency', 3)->default('TRY')->after('currency_code');
            $table->decimal('source_price', 10, 2)->after('price');
            
            // Index for performance
            $table->index(['source_currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['source_currency']);
            $table->dropColumn(['source_currency', 'source_price']);
        });
    }
};
