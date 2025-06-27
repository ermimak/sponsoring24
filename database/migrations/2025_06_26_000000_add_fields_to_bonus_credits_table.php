<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBonusCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bonus_credits', function (Blueprint $table) {
            // Add missing columns needed for referral bonus crediting
            if (!Schema::hasColumn('bonus_credits', 'credited')) {
                $table->boolean('credited')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('bonus_credits', 'payment_id')) {
                $table->string('payment_id')->nullable()->after('credited');
            }
            
            if (!Schema::hasColumn('bonus_credits', 'credited_at')) {
                $table->timestamp('credited_at')->nullable()->after('payment_id');
            }
            
            if (!Schema::hasColumn('bonus_credits', 'type')) {
                $table->string('type')->default('referral')->after('referral_code_used');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bonus_credits', function (Blueprint $table) {
            $table->dropColumn(['credited', 'payment_id', 'credited_at', 'type']);
        });
    }
}
