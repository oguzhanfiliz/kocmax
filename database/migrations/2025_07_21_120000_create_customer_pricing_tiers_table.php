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
        Schema::create('customer_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['b2b', 'b2c', 'wholesale', 'retail', 'guest'])->default('b2c');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->integer('min_quantity')->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['priority', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_pricing_tiers');
    }
};