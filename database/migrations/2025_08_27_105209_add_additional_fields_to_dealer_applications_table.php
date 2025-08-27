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
        Schema::table('dealer_applications', function (Blueprint $table) {
            $table->string('authorized_person_name')->after('company_name');
            $table->string('authorized_person_phone', 20)->after('authorized_person_name');
            $table->string('tax_office')->after('tax_number');
            $table->text('address')->after('tax_office');
            $table->string('landline_phone', 20)->nullable()->after('address');
            $table->string('website')->nullable()->after('landline_phone');
            $table->string('email')->after('website');
            $table->string('business_field')->after('email');
            $table->text('reference_companies')->nullable()->after('business_field');
            
            // İndeksler ekle
            $table->index('authorized_person_phone');
            $table->index('email');
            $table->index('business_field');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealer_applications', function (Blueprint $table) {
            $table->dropIndex(['authorized_person_phone']);
            $table->dropIndex(['email']);
            $table->dropIndex(['business_field']);
            
            $table->dropColumn([
                'authorized_person_name',
                'authorized_person_phone',
                'tax_office',
                'address',
                'landline_phone',
                'website',
                'email',
                'business_field',
                'reference_companies'
            ]);
        });
    }
};
