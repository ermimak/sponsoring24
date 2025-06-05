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
        // Skip creation if the table already exists
        if (Schema::hasTable('contents')) {
            return;
        }
        
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('section')->unique();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->json('content');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
