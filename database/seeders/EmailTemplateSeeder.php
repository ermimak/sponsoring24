<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create a project to associate with email templates
        $project = Project::first();
        
        if (!$project) {
            // Find or create a user to associate with the project
            $user = User::first();
            
            if (!$user) {
                $user = User::create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
            }
            
            // Create a test project
            $project = Project::create([
                'name' => 'Test Project',
                'description' => 'A test project for email templates',
                'user_id' => $user->id,
                'currency' => 'USD',
                'target_amount' => 10000,
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'status' => 'active',
            ]);
            
            $this->command->info('Created test project: ' . $project->name);
        }
        
        $templates = [
            [
                'name' => 'Participant Welcome',
                'type' => 'participant',
                'subject' => 'Welcome to the project, {{participant_first_name}}!',
                'body' => "Hello {{participant_first_name}} {{participant_last_name}},\n\nWelcome to {{project_name}}! We're excited to have you on board.\n\nYou can access your landing page here: {{landing_page_url}}\n\nBest regards,\nThe {{project_name}} Team",
                'project_id' => $project->id,
            ],
            [
                'name' => 'Donation Thank You',
                'type' => 'donation',
                'subject' => 'Thank you for your donation to {{project_name}}!',
                'body' => "Dear {{donor_name}},\n\nThank you for your generous donation of {{amount}} {{currency}} to support {{participant_first_name}} {{participant_last_name}} in {{project_name}}.\n\nYour contribution makes a real difference!\n\nBest regards,\nThe {{project_name}} Team",
                'project_id' => $project->id,
            ],
            [
                'name' => 'Mass Participant Update',
                'type' => 'mass_participant',
                'subject' => 'Important update from {{project_name}}',
                'body' => "Hello {{participant_first_name}},\n\nWe have an important update about {{project_name}} to share with you.\n\nYour current donation total is: {{participant_donation_total}} {{currency}}.\n\nKeep up the great work!\n\nBest regards,\nThe {{project_name}} Team",
                'project_id' => $project->id,
            ],
            [
                'name' => 'Mass Donor Update',
                'type' => 'mass_donation',
                'subject' => 'Update from {{project_name}}',
                'body' => "Dear {{donor_name}},\n\nWe wanted to provide you with an update on {{project_name}} that you've supported.\n\nYour donation of {{amount}} {{currency}} is helping us achieve our goals.\n\nThank you for your continued support!\n\nBest regards,\nThe {{project_name}} Team",
                'project_id' => $project->id,
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['name' => $template['name'], 'type' => $template['type'], 'project_id' => $project->id],
                $template
            );
        }

        $this->command->info('Email templates seeded successfully!');
    }
}
