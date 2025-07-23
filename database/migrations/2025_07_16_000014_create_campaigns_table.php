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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // Campaign type enum value
            $table->string('status')->default('draft'); // Campaign status enum value
            $table->json('rules')->nullable(); // Campaign kuralları
            $table->json('rewards')->nullable(); // Hediye/indirim detayları
            $table->json('conditions')->nullable(); // Koşullar (min sepet tutarı vs)
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_stackable')->default(false); // Diğer kampanyalarla birleştirilebilir mi
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->integer('usage_limit')->nullable(); // Toplam kullanım sınırı
            $table->integer('usage_count')->default(0); // Şu anki kullanım sayısı
            $table->integer('usage_limit_per_customer')->nullable(); // Müşteri başına kullanım sınırı
            $table->decimal('minimum_cart_amount', 12, 2)->nullable();
            $table->json('customer_types')->nullable(); // ['b2b', 'b2c', 'guest']
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['is_active', 'starts_at', 'ends_at']);
            $table->index(['type', 'status', 'is_active']);
            $table->index('priority');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
