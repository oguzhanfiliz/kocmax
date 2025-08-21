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
        Schema::create('popular_searches', function (Blueprint $table) {
            $table->id();
            $table->string('query')->unique();
            $table->integer('count')->default(1);
            $table->timestamps();
            
            // İndeksler
            $table->index(['count', 'query']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popular_searches');
    }
};