<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('organization_name')->nullable();
            $table->string('contact_title')->nullable();
            $table->string('contact_first_name')->nullable();
            $table->string('contact_last_name')->nullable();
            $table->string('address')->nullable();
            $table->string('address_suffix')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('accent_color')->default('#9500FF');
            $table->string('logo_path')->nullable();
            $table->string('billing_salutation')->nullable();
            $table->string('billing_first_name')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_address_suffix')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_location')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->text('bank_account_details')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('iban')->nullable();
            $table->string('recipient')->nullable();
            $table->boolean('project_overview_enabled')->default(false);
            $table->char('user_id', 36)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
