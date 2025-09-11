<?php

declare(strict_types=1);

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
        // Doctrine DBAL olmadan güvenli ALTER TABLE
        DB::statement("ALTER TABLE dealer_applications MODIFY trade_registry_document_path VARCHAR(255) NULL");
        DB::statement("ALTER TABLE dealer_applications MODIFY tax_plate_document_path VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE dealer_applications MODIFY trade_registry_document_path VARCHAR(255) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE dealer_applications MODIFY tax_plate_document_path VARCHAR(255) NOT NULL DEFAULT ''");
    }
};


