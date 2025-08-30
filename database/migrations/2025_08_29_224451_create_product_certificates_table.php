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
        Schema::create('product_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name'); // Sertifika adı (örn: "CE Sertifikası", "ISO 9001")
            $table->string('file_path'); // Dosya yolu
            $table->string('file_name')->nullable(); // Orijinal dosya adı
            $table->string('file_type')->nullable(); // Dosya türü (pdf, doc, xls, png, jpg, vb.)
            $table->integer('file_size')->nullable(); // Dosya boyutu (bytes)
            $table->text('description')->nullable(); // Sertifika açıklaması
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['product_id', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_certificates');
    }
};
