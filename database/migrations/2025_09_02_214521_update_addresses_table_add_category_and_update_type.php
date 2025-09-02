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
        // Step 1: Add category as string first
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('category', 20)->default('both')->after('type');
        });

        // Step 2: Migrate existing data
        DB::statement("UPDATE addresses SET category = type WHERE type IS NOT NULL");
        
        // Step 3: Add temporary type column as string
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('type_new', 20)->default('other')->after('category');
        });
        
        // Step 4: Convert existing type data to new values
        DB::statement("UPDATE addresses SET type_new = 'other' WHERE type IS NOT NULL");
        
        // Step 5: Drop old type column
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        // Step 6: Rename new column to type
        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('type_new', 'type');
        });
        
        // Step 7: Convert strings to enums using raw SQL
        if (Schema::hasTable('addresses')) {
            // For MySQL
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE addresses MODIFY COLUMN category ENUM('shipping', 'billing', 'both') DEFAULT 'both'");
                DB::statement("ALTER TABLE addresses MODIFY COLUMN type ENUM('home', 'work', 'billing', 'other') DEFAULT 'other'");
            } else {
                // For other databases, keep as string with constraints
                DB::statement("ALTER TABLE addresses ADD CONSTRAINT addresses_category_check CHECK (category IN ('shipping', 'billing', 'both'))");
                DB::statement("ALTER TABLE addresses ADD CONSTRAINT addresses_type_check CHECK (type IN ('home', 'work', 'billing', 'other'))");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add temporary column for old type values
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('type_old', 20)->default('both')->after('type');
        });
        
        // Step 2: Convert data back to old format
        DB::statement("UPDATE addresses SET type_old = category WHERE category IS NOT NULL");
        
        // Step 3: Drop current columns
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['category', 'type']);
        });
        
        // Step 4: Rename column back
        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('type_old', 'type');
        });
        
        // Step 5: Convert back to enum
        if (Schema::hasTable('addresses')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE addresses MODIFY COLUMN type ENUM('shipping', 'billing', 'both') DEFAULT 'both'");
            } else {
                DB::statement("ALTER TABLE addresses ADD CONSTRAINT addresses_type_old_check CHECK (type IN ('shipping', 'billing', 'both'))");
            }
        }
    }
};
