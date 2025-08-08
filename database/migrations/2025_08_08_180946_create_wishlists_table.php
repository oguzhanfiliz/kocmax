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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamp('added_at')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->tinyInteger('priority')->default(2); // 1=Low, 2=Medium, 3=High, 4=Urgent
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'is_favorite']);
            $table->index(['user_id', 'priority']);
            $table->index('notification_sent_at');

            // Unique constraint to prevent duplicate wishlist items
            $table->unique(['user_id', 'product_id', 'product_variant_id'], 'wishlist_unique_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};