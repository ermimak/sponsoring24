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
        // First add the column
        Schema::table('bonus_credits', function (Blueprint $table) {
            $table->string('type')->nullable()->after('status');
        });
        
        // Then update the data in a separate statement
        DB::statement("UPDATE bonus_credits SET type = 'referral' WHERE referral_code_used IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonus_credits', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
