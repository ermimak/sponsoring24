<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for UUID foreign keys to improve lookup performance
        Schema::table('donations', function (Blueprint $table) {
            // Only add indexes that don't already exist
            if (!$this->indexExists('donations', 'idx_donations_status')) {
                $table->index('status', 'idx_donations_status');
            }
            if (!$this->indexExists('donations', 'idx_donations_payment_method')) {
                $table->index('payment_method', 'idx_donations_payment_method');
            }
        });

        Schema::table('participants', function (Blueprint $table) {
            if (!$this->indexExists('participants', 'idx_participants_email')) {
                $table->index('email', 'idx_participants_email');
            }
            if (!$this->indexExists('participants', 'idx_participants_created_at')) {
                $table->index('created_at', 'idx_participants_created_at');
            }
        });

        Schema::table('participant_project', function (Blueprint $table) {
            if (!$this->indexExists('participant_project', 'idx_participant_project_participant')) {
                $table->index('participant_id', 'idx_participant_project_participant');
            }
            if (!$this->indexExists('participant_project', 'idx_participant_project_project')) {
                $table->index('project_id', 'idx_participant_project_project');
            }
            if (!$this->indexExists('participant_project', 'idx_participant_project_composite')) {
                $table->index(['participant_id', 'project_id'], 'idx_participant_project_composite');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            if (!$this->indexExists('projects', 'idx_projects_created_by')) {
                $table->index('created_by', 'idx_projects_created_by');
            }
            if (!$this->indexExists('projects', 'idx_projects_start')) {
                $table->index('start', 'idx_projects_start');
            }
            if (!$this->indexExists('projects', 'idx_projects_end')) {
                $table->index('end', 'idx_projects_end');
            }
            if (!$this->indexExists('projects', 'idx_projects_created_at')) {
                $table->index('created_at', 'idx_projects_created_at');
            }
        });

        Schema::table('supporters', function (Blueprint $table) {
            if (!$this->indexExists('supporters', 'idx_supporters_email')) {
                $table->index('email', 'idx_supporters_email');
            }
            if (!$this->indexExists('supporters', 'idx_supporters_created_at')) {
                $table->index('created_at', 'idx_supporters_created_at');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'idx_users_email')) {
                $table->index('email', 'idx_users_email');
            }
            if (!$this->indexExists('users', 'idx_users_referral_code')) {
                $table->index('referral_code', 'idx_users_referral_code');
            }
            if (!$this->indexExists('users', 'idx_users_approval_status')) {
                $table->index('approval_status', 'idx_users_approval_status');
            }
            if (!$this->indexExists('users', 'idx_users_created_at')) {
                $table->index('created_at', 'idx_users_created_at');
            }
        });

        Schema::table('bonus_credits', function (Blueprint $table) {
            if (!$this->indexExists('bonus_credits', 'idx_bonus_credits_user_id')) {
                $table->index('user_id', 'idx_bonus_credits_user_id'); // This is the referrer
            }
            if (!$this->indexExists('bonus_credits', 'idx_bonus_credits_referred_user')) {
                $table->index('referred_user_id', 'idx_bonus_credits_referred_user');
            }
            if (!$this->indexExists('bonus_credits', 'idx_bonus_credits_created_at')) {
                $table->index('created_at', 'idx_bonus_credits_created_at');
            }
        });

        Schema::table('email_templates', function (Blueprint $table) {
            $table->index('type', 'idx_email_templates_type');
            $table->index('project_id', 'idx_email_templates_project');
        });

        Schema::table('member_groups', function (Blueprint $table) {
            $table->index('name', 'idx_member_groups_name');
            $table->index('created_at', 'idx_member_groups_created_at');
        });

        Schema::table('member_group_participant', function (Blueprint $table) {
            $table->index('member_group_id', 'idx_mgp_member_group');
            $table->index('participant_id', 'idx_mgp_participant');
            $table->index(['member_group_id', 'participant_id'], 'idx_mgp_composite');
        });

        // Add indexes for audit trail tables if they exist
        if (Schema::hasTable('user_activities')) {
            Schema::table('user_activities', function (Blueprint $table) {
                $table->index('user_id', 'idx_user_activities_user');
                $table->index('activity_type', 'idx_user_activities_type');
                $table->index('created_at', 'idx_user_activities_created_at');
                $table->index(['user_id', 'activity_type'], 'idx_user_activities_user_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex('idx_donations_participant_id');
            $table->dropIndex('idx_donations_project_id');
            $table->dropIndex('idx_donations_supporter_id');
            $table->dropIndex('idx_donations_project_participant');
            $table->dropIndex('idx_donations_created_at');
            $table->dropIndex('idx_donations_status');
            $table->dropIndex('idx_donations_payment_method');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex('idx_participants_email');
            $table->dropIndex('idx_participants_created_at');
        });

        Schema::table('participant_project', function (Blueprint $table) {
            $table->dropIndex('idx_participant_project_participant');
            $table->dropIndex('idx_participant_project_project');
            $table->dropIndex('idx_participant_project_composite');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_created_by');
            $table->dropIndex('idx_projects_start');
            $table->dropIndex('idx_projects_end');
            $table->dropIndex('idx_projects_created_at');
        });

        Schema::table('supporters', function (Blueprint $table) {
            $table->dropIndex('idx_supporters_email');
            $table->dropIndex('idx_supporters_created_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_referral_code');
            $table->dropIndex('idx_users_approval_status');
            $table->dropIndex('idx_users_created_at');
        });

        Schema::table('bonus_credits', function (Blueprint $table) {
            $table->dropIndex('idx_bonus_credits_referrer');
            $table->dropIndex('idx_bonus_credits_referred_user');
            $table->dropIndex('idx_bonus_credits_created_at');
        });

        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropIndex('idx_email_templates_type');
            $table->dropIndex('idx_email_templates_project');
        });

        Schema::table('member_groups', function (Blueprint $table) {
            $table->dropIndex('idx_member_groups_name');
            $table->dropIndex('idx_member_groups_created_at');
        });

        Schema::table('member_group_participant', function (Blueprint $table) {
            $table->dropIndex('idx_mgp_member_group');
            $table->dropIndex('idx_mgp_participant');
            $table->dropIndex('idx_mgp_composite');
        });

        if (Schema::hasTable('user_activities')) {
            Schema::table('user_activities', function (Blueprint $table) {
                $table->dropIndex('idx_user_activities_user');
                $table->dropIndex('idx_user_activities_type');
                $table->dropIndex('idx_user_activities_created_at');
                $table->dropIndex('idx_user_activities_user_type');
            });
        }
    }
};
