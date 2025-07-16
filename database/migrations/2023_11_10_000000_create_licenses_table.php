<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('license_key')->unique();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->enum('type', ['annual', 'monthly', 'lifetime'])->default('annual');
            $table->timestamp('issued_at');
            $table->timestamp('expires_at')->nullable();
            $table->string('payment_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('CHF');
            $table->boolean('discount_applied')->default(false);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('licenses');
    }
};
