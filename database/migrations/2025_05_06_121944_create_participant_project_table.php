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
        Schema::create('participant_project', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('participant_id', 36);
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            $table->char('project_id', 36);
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->string('role')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_project');
    }
};
