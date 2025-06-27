<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ProjectUpdateNotification;
use App\Models\Project;
use Illuminate\Console\Command;

class CreateTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {user_id? : The ID of the user to send notifications to} {type=all : The type of notification to create (project, payment, approval, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications for testing the notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $type = $this->argument('type');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
            $users = collect([$user]);
        } else {
            // Get all admin users
            $users = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })->get();
            
            if ($users->isEmpty()) {
                $this->error("No admin users found.");
                return 1;
            }
        }
        
        // Get a random project or create one if none exists
        $project = Project::inRandomOrder()->first();
        if (!$project) {
            $this->info("No projects found. Creating a test project.");
            $project = Project::create([
                'name' => 'Test Project',
                'description' => 'This is a test project for notifications',
                'language' => 'en',
            ]);
        }
        
        foreach ($users as $user) {
            $this->createNotifications($user, $project, $type);
        }
        
        $this->info("Test notifications created successfully for " . $users->count() . " users.");
        return 0;
    }
    
    /**
     * Create notifications for a user
     */
    private function createNotifications(User $user, Project $project, string $type)
    {
        if ($type === 'project' || $type === 'all') {
            $user->notify(new ProjectUpdateNotification($project, 'created'));
            $this->info("Project notification sent to {$user->email}");
        }
        
        if ($type === 'payment' || $type === 'all') {
            $user->notify(new \App\Notifications\PaymentReceivedNotification(
                (object)['id' => 'test-payment-' . rand(1000, 9999)],
                rand(100, 1000),
                $project->name
            ));
            $this->info("Payment notification sent to {$user->email}");
        }
        
        if ($type === 'donation' || $type === 'all') {
            $user->notify(new \App\Notifications\NewDonationNotification(
                (object)['id' => 'test-donation-' . rand(1000, 9999)],
                rand(50, 500),
                $project->name,
                'Test Donor'
            ));
            $this->info("Donation notification sent to {$user->email}");
        }
        
        if ($type === 'approval' || $type === 'all') {
            $user->notify(new \App\Notifications\UserApprovalNotification());
            $this->info("Approval notification sent to {$user->email}");
        }
    }
}
