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
        Schema::create('bonus_credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Referrer
            $table->unsignedBigInteger('referred_user_id'); // Referred user
            $table->decimal('amount', 8, 2)->default(100.00);
            $table->string('status')->default('pending'); // pending, credited
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referred_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_credits');
    }
};
