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
            $table->string('currency_code', 3)->default('TRY')->after('price');
            $table->foreign('currency_code')->references('code')->on('currencies')->onDelete('restrict');
            $table->index(['currency_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['currency_code']);
            $table->dropIndex(['currency_code', 'is_active']);
            $table->dropColumn('currency_code');
        });
    }
};
