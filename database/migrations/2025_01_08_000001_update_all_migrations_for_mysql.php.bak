<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to update all remaining UUID columns for MySQL compatibility.
     */
    public function up(): void
    {
        // Update participants table
        if (Schema::hasTable('participants')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->char('id', 36)->change();
                if (Schema::hasColumn('participants', 'created_by')) {
                    $table->char('created_by', 36)->nullable()->change();
                }
            });
        }

        // Update supporters table
        if (Schema::hasTable('supporters')) {
            Schema::table('supporters', function (Blueprint $table) {
                $table->char('id', 36)->change();
            });
        }

        // Update donations table
        if (Schema::hasTable('donations')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->char('id', 36)->change();
                $table->char('project_id', 36)->change();
                $table->char('participant_id', 36)->change();
                $table->char('supporter_id', 36)->nullable()->change();
            });
        }

        // Update licenses table
        if (Schema::hasTable('licenses')) {
            Schema::table('licenses', function (Blueprint $table) {
                $table->char('id', 36)->change();
                $table->char('user_id', 36)->change();
            });
        }

        // Update email_templates table
        if (Schema::hasTable('email_templates')) {
            Schema::table('email_templates', function (Blueprint $table) {
                $table->char('id', 36)->change();
                if (Schema::hasColumn('email_templates', 'project_id')) {
                    $table->char('project_id', 36)->nullable()->change();
                }
            });
        }

        // Update bonus_credits table
        if (Schema::hasTable('bonus_credits')) {
            Schema::table('bonus_credits', function (Blueprint $table) {
                $table->char('id', 36)->change();
                $table->char('user_id', 36)->change();
                if (Schema::hasColumn('bonus_credits', 'referrer_id')) {
                    $table->char('referrer_id', 36)->nullable()->change();
                }
            });
        }

        // Update member_groups table
        if (Schema::hasTable('member_groups')) {
            Schema::table('member_groups', function (Blueprint $table) {
                $table->char('id', 36)->change();
                if (Schema::hasColumn('member_groups', 'created_by')) {
                    $table->char('created_by', 36)->nullable()->change();
                }
            });
        }

        // Update pivot tables
        if (Schema::hasTable('member_group_participant')) {
            Schema::table('member_group_participant', function (Blueprint $table) {
                $table->char('id', 36)->change();
                $table->char('member_group_id', 36)->change();
                $table->char('participant_id', 36)->change();
            });
        }

        if (Schema::hasTable('participant_project')) {
            Schema::table('participant_project', function (Blueprint $table) {
                $table->char('id', 36)->change();
                $table->char('participant_id', 36)->change();
                $table->char('project_id', 36)->change();
            });
        }

        // Update personal_access_tokens table
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->char('id', 36)->change();
                $table->char('tokenable_id', 36)->change();
            });
        }

        // Update permission tables (Spatie)
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->char('id', 36)->change();
            });
        }

        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->char('id', 36)->change();
                if (Schema::hasColumn('roles', 'team_id')) {
                    $table->char('team_id', 36)->nullable()->change();
                }
            });
        }

        // Update other tables
        $otherTables = ['activities', 'news', 'contents', 'settings', 'user_activities'];
        foreach ($otherTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->char('id', 36)->change();
                    if (Schema::hasColumn($table->getTable(), 'user_id')) {
                        $table->char('user_id', 36)->nullable()->change();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it changes data types for compatibility
    }
};
