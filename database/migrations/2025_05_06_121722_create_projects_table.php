<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->json('name'); // translatable
            $table->json('description')->nullable(); // translatable
            $table->string('location')->nullable();
            $table->string('language', 5)->default('de');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->dateTime('allow_donation_until')->nullable();
            $table->string('image_landscape')->nullable();
            $table->string('image_square')->nullable();
            $table->boolean('flat_rate_enabled')->default(false);
            $table->decimal('flat_rate_min_amount', 10, 2)->nullable();
            $table->string('flat_rate_help_text')->nullable();
            $table->boolean('unit_based_enabled')->default(false);
            $table->boolean('public_donation_enabled')->default(false);
            $table->char('created_by', 36)->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
