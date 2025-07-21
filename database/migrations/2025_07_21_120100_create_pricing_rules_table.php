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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['percentage', 'fixed_amount', 'tiered', 'bulk'])->default('percentage');
            $table->text('description')->nullable();
            
            // Rule conditions (JSON)
            $table->json('conditions'); // {"min_quantity": 10, "customer_type": "b2b", "category_ids": [1,2,3]}
            
            // Rule actions (JSON)  
            $table->json('actions'); // {"discount_percentage": 15, "discount_amount": 100}
            
            // Priority and status
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_stackable')->default(false); // Can combine with other discounts
            $table->boolean('is_exclusive')->default(false); // Exclusive discount
            
            // Time constraints
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            
            // Usage limits
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_count')->default(0);
            $table->integer('usage_limit_per_customer')->nullable();
            
            // Relations
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['priority', 'is_active']);
            $table->index(['starts_at', 'ends_at', 'is_active']);
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};