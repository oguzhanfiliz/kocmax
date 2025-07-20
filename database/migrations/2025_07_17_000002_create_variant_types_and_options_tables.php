<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Variant Types (Renk, Beden, vb.)
        Schema::create('variant_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Color, Size, Material, etc.
            $table->string('slug')->unique();
            $table->string('display_name'); // Renk, Beden, Malzeme
            $table->enum('input_type', ['select', 'radio', 'color', 'image'])->default('select');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Variant Options (Kırmızı, Mavi, S, M, L, vb.)
        Schema::create('variant_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_type_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Red, Blue, Small, Medium, Large
            $table->string('value')->nullable(); // Kırmızı, Mavi, S, M, L
            $table->string('slug');
            $table->string('hex_color', 7)->nullable(); // For color type
            $table->string('image_url')->nullable(); // For image type
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['variant_type_id', 'is_active', 'sort_order']);
            $table->unique(['variant_type_id', 'slug']);
        });

        // Product Variant Options (Many-to-Many)
        Schema::create('product_variant_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_option_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_variant_id', 'variant_option_id'], 'product_variant_option_unique');
            $table->index('variant_option_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_options');
        Schema::dropIfExists('variant_options');
        Schema::dropIfExists('variant_types');
    }
};
