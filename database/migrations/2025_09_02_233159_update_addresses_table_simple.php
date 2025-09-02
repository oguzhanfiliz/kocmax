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
        // Check if category column exists first
        if (!Schema::hasColumn('addresses', 'category')) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->string('category', 20)->default('both')->after('type')->comment('shipping, billing, both');
            });
        }

        // Copy existing type values to category if category is empty
        DB::statement("UPDATE addresses SET category = type WHERE category IS NULL OR category = '' AND type IS NOT NULL");
        
        // Update existing type values to new format
        DB::statement("UPDATE addresses SET type = 'other' WHERE type IN ('shipping', 'billing', 'both')");
        
        // Add index for better performance if it doesn't exist
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('addresses');
        if (!isset($indexes['addresses_user_id_category_index'])) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->index(['user_id', 'category'], 'addresses_user_id_category_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('addresses');
        if (isset($indexes['addresses_user_id_category_index'])) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->dropIndex('addresses_user_id_category_index');
            });
        }
        
        // Drop category column if exists
        if (Schema::hasColumn('addresses', 'category')) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
        
        // Restore old type values if needed
        DB::statement("UPDATE addresses SET type = 'both' WHERE type = 'other'");
    }
};