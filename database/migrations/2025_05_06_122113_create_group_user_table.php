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
        Schema::create('member_groups', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('member_group_participant', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('member_group_id', 36);
            $table->foreign('member_group_id')->references('id')->on('member_groups')->onDelete('cascade');
            $table->char('participant_id', 36);
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_user');
    }
};
