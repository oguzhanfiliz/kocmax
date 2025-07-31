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
        Schema::table('orders', function (Blueprint $table) {
            // Add cart_id foreign key to track which cart the order came from
            $table->unsignedBigInteger('cart_id')->nullable()->after('user_id');
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('set null');
            
            // Add index for performance
            $table->index('cart_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropIndex(['cart_id']);
            $table->dropColumn('cart_id');
        });
    }
};