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
        Schema::create('pricing_rule_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_rule_id')->constrained('pricing_rules')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['pricing_rule_id', 'category_id']);
            
            // Indexes
            $table->index('pricing_rule_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rule_categories');
    }
};