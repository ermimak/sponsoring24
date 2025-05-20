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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('billing_country')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_address_suffix')->nullable();
            $table->string('accent_color')->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'billing_country',
                'billing_last_name',
                'billing_address_suffix',
                'accent_color',
                'country',
                'language',
                'two_factor_enabled'
            ]);
        });
    }
};
