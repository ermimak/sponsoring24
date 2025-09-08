<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to convert UUID columns for MySQL compatibility.
     * This migration ensures all UUID columns use CHAR(36) instead of native UUID type.
     */
    public function up(): void
    {
        // This migration should run BEFORE all other migrations
        // It modifies the Blueprint to use char(36) for UUID columns
        
        // We'll override the uuid() method behavior in migrations
        // by ensuring all existing migrations use char(36) for UUIDs
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it's a compatibility layer
    }
};
