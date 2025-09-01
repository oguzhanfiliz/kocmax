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
            // Kutu boyutları - varyant seviyesinde override edilebilir
            $table->integer('box_quantity')->nullable()->after('height')->comment('Kutu adeti (varyant seviyesi)');
            $table->decimal('product_weight', 8, 3)->nullable()->after('box_quantity')->comment('Ürün ağırlığı (gr) (varyant seviyesi)');
            
            // Koli boyutları - varyant seviyesinde override edilebilir
            $table->integer('package_quantity')->nullable()->after('product_weight')->comment('Koli adeti (varyant seviyesi)');
            $table->decimal('package_weight', 8, 3)->nullable()->after('package_quantity')->comment('Koli ağırlığı (kg) (varyant seviyesi)');
            $table->decimal('package_length', 8, 1)->nullable()->after('package_weight')->comment('Koli uzunluğu (cm) (varyant seviyesi)');
            $table->decimal('package_width', 8, 1)->nullable()->after('package_length')->comment('Koli genişliği (cm) (varyant seviyesi)');
            $table->decimal('package_height', 8, 1)->nullable()->after('package_width')->comment('Koli yüksekliği (cm) (varyant seviyesi)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'box_quantity',
                'product_weight', 
                'package_quantity',
                'package_weight',
                'package_length',
                'package_width',
                'package_height'
            ]);
        });
    }
};