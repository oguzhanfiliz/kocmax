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
        // Attribute types table
        Schema::create('attribute_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // text, select, checkbox, radio, color, etc.
            $table->string('display_name');
            $table->string('component'); // Filament component name
            $table->json('config')->nullable(); // Additional configuration
            $table->timestamps();
        });

        // Product attributes table
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('attribute_type_id')->constrained('attribute_types');
            $table->json('options')->nullable(); // For select, checkbox, radio options
            $table->boolean('is_required')->default(false);
            $table->boolean('is_variant')->default(false); // Can be used for variant generation
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_variant');
            $table->index('is_active');
        });

        // Category attributes pivot table
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['category_id', 'product_attribute_id']);
        });

        // Product attribute values
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete();
            $table->text('value'); // Stores any type of value as text/json
            $table->timestamps();
            
            $table->index(['product_id', 'product_attribute_id']);
        });

        // Variant attribute values
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete();
            $table->text('value');
            $table->timestamps();
            
            $table->unique(['product_variant_id', 'product_attribute_id'], 'variant_attribute_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('category_attributes');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('attribute_types');
    }
};
