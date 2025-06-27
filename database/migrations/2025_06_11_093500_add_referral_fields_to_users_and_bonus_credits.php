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
        // Add discount eligibility field to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('discount_eligible')->default(false);
            $table->boolean('discount_used')->default(false);
        });

        // Add referral code field to bonus_credits table if it doesn't exist
        if (!Schema::hasColumn('bonus_credits', 'referral_code_used')) {
            Schema::table('bonus_credits', function (Blueprint $table) {
                $table->string('referral_code_used')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('discount_eligible');
            $table->dropColumn('discount_used');
        });

        if (Schema::hasColumn('bonus_credits', 'referral_code_used')) {
            Schema::table('bonus_credits', function (Blueprint $table) {
                $table->dropColumn('referral_code_used');
            });
        }
    }
};
