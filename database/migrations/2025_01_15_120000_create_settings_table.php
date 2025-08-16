<?php

declare(strict_types=1);

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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index('idx_settings_key');
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, float, boolean, array, json
            $table->string('group')->nullable()->index('idx_settings_group'); // pricing, campaign, system, etc.
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Frontend'de gösterilsin mi?
            $table->boolean('is_encrypted')->default(false); // Şifrelenmesi gerekir mi?
            $table->json('validation_rules')->nullable(); // Laravel validation rules
            $table->json('options')->nullable(); // Select options, min/max values, etc.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            
            // Indexes
            $table->index(['group', 'key'], 'idx_settings_group_key');
            $table->index(['is_public'], 'idx_settings_public');
            $table->index('created_at', 'idx_settings_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
