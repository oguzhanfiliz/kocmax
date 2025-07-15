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
        Schema::table('users', function (Blueprint $table) {
            $table->string('dealer_code')->nullable()->unique();
            $table->string('company_name')->nullable();
            $table->string('tax_number')->nullable();
            $table->decimal('dealer_discount_percentage', 5, 2)->nullable();
            $table->boolean('is_approved_dealer')->default(false);
            $table->timestamp('dealer_application_date')->nullable();
            $table->timestamp('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'dealer_code',
                'company_name',
                'tax_number',
                'dealer_discount_percentage',
                'is_approved_dealer',
                'dealer_application_date',
                'approved_at'
            ]);
        });
    }
};
