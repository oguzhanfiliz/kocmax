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
            $table->decimal('length', 8, 1)->nullable()->after('weight')->comment('Uzunluk (cm)');
            $table->decimal('width', 8, 1)->nullable()->after('length')->comment('Genişlik (cm)');
            $table->decimal('height', 8, 1)->nullable()->after('width')->comment('Yükseklik (cm)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['length', 'width', 'height']);
        });
    }
};
