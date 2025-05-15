<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToEmailTemplatesTable extends Migration
{
    public function up()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('footer')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('show_logo')->default(false);
            $table->boolean('show_header_image')->default(false);
            $table->boolean('show_placeholders')->default(false);
            $table->string('regarding')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('sender_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn(['footer', 'notes', 'show_logo', 'show_header_image', 'show_placeholders', 'regarding', 'reply_to', 'sender_name']);
        });
    }
}