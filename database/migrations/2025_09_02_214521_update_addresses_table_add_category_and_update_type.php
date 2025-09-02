<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Yeni category alanı ekle
            $table->enum('category', ['shipping', 'billing', 'both'])->default('both')->after('type');
            
            // Mevcut type enum değerlerini güncelle
            // Önce yeni column ekleyip sonra eski type'ı güncelleyeceğiz
        });

        // Mevcut verileri yeni formata dönüştür
        DB::statement("UPDATE addresses SET category = type");
        
        // Type alanını yeni değerlerle güncelle
        Schema::table('addresses', function (Blueprint $table) {
            // Önce default değer ver
            $table->string('type_temp')->default('other')->after('type');
        });
        
        // Veriyi dönüştür
        DB::statement("UPDATE addresses SET type_temp = 'other' WHERE type IS NOT NULL");
        
        // Eski type sütununu sil ve yenisini yeniden adlandır
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('type_temp', 'type');
        });
        
        // Type'ı enum yap
        Schema::table('addresses', function (Blueprint $table) {
            $table->enum('type', ['home', 'work', 'billing', 'other'])->default('other')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Category alanını sil
            $table->dropColumn('category');
            
            // Type'ı eski haline getir
            $table->enum('type', ['shipping', 'billing', 'both'])->default('both')->change();
        });
    }
};
