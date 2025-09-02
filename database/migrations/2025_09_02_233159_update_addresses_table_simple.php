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
        // Add category column as string (avoid enum issues)
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('category', 20)->default('both')->after('type')->comment('shipping, billing, both');
        });

        // Copy existing type values to category
        DB::statement("UPDATE addresses SET category = type WHERE type IS NOT NULL");
        
        // Update existing type values to new format
        DB::statement("UPDATE addresses SET type = 'other' WHERE type IN ('shipping', 'billing', 'both')");
        
        // Add index for better performance
        Schema::table('addresses', function (Blueprint $table) {
            $table->index(['user_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'category']);
            $table->dropColumn('category');
        });
        
        // Restore old type values if needed
        // DB::statement("UPDATE addresses SET type = 'both' WHERE type = 'other'");
    }
};