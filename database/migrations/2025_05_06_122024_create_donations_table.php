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
        Schema::create('donations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->uuid('participant_id')->nullable();
            $table->foreign('participant_id')->references('id')->on('participants')->nullOnDelete();
            $table->uuid('supporter_id')->nullable(); // supporter user or contact
            $table->foreign('supporter_id')->references('id')->on('supporters')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('type'); // flat, unit, etc.
            $table->date('billing_date')->nullable();
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('currency', 3)->default('CHF');
            $table->string('confirmation_token')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('payment_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
