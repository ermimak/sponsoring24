<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add UUID column to settings table
        Schema::table('settings', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Generate UUIDs for existing settings
        DB::table('settings')->whereNull('uuid')->update([
            'uuid' => DB::raw('gen_random_uuid()')
        ]);

        // Make UUID column not nullable
        Schema::table('settings', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });

        // Add unique constraint to UUID column
        Schema::table('settings', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove UUID column from settings table
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
