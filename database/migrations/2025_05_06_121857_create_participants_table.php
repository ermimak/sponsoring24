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
        Schema::create('participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->string('address_suffix')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->date('birthday')->nullable();
            $table->string('email')->unique();
            $table->string('email_cc')->nullable();
            $table->string('phone')->nullable();
            $table->string('member_id')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
