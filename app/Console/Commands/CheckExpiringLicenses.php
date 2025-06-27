<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Notifications\LicenseNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiringLicenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licenses:check-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for licenses that are about to expire and notify users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for expiring licenses...');
        
        // Check for licenses expiring in 30 days
        $this->checkLicensesExpiringInDays(30);
        
        // Check for licenses expiring in 7 days
        $this->checkLicensesExpiringInDays(7);
        
        // Check for licenses expiring in 1 day
        $this->checkLicensesExpiringInDays(1);
        
        // Check for licenses that have expired yesterday
        $this->checkExpiredLicenses();
        
        $this->info('License expiration check completed.');
        
        return 0;
    }
    
    /**
     * Check for licenses expiring in the specified number of days.
     *
     * @param int $days
     * @return void
     */
    private function checkLicensesExpiringInDays(int $days)
    {
        $date = Carbon::now()->addDays($days)->startOfDay();
        $nextDay = Carbon::now()->addDays($days + 1)->startOfDay();
        
        $licenses = License::where('status', 'active')
            ->whereBetween('expires_at', [$date, $nextDay])
            ->get();
            
        $this->info("Found {$licenses->count()} licenses expiring in {$days} days.");
        
        foreach ($licenses as $license) {
            $user = $license->user;
            
            if ($user) {
                $user->notify(new LicenseNotification($user, 'license_expiring', [
                    'days_left' => $days,
                    'expires_at' => $license->expires_at->format('Y-m-d'),
                    'license_key' => $license->license_key,
                    'license_type' => $license->type,
                ]));
                
                $this->info("Notified user {$user->id} about license {$license->license_key} expiring in {$days} days.");
                Log::info("License expiration notification sent", [
                    'user_id' => $user->id,
                    'license_id' => $license->id,
                    'days_remaining' => $days,
                ]);
            }
        }
    }
    
    /**
     * Check for licenses that have expired yesterday.
     *
     * @return void
     */
    private function checkExpiredLicenses()
    {
        $yesterday = Carbon::now()->subDay()->endOfDay();
        
        $licenses = License::where('status', 'active')
            ->where('expires_at', '<=', $yesterday)
            ->get();
            
        $this->info("Found {$licenses->count()} licenses that have expired.");
        
        foreach ($licenses as $license) {
            // Update license status to expired
            $license->status = 'expired';
            $license->save();
            
            $user = $license->user;
            
            if ($user) {
                $user->notify(new LicenseNotification($user, 'license_expired', [
                    'license_key' => $license->license_key,
                    'license_type' => $license->type,
                    'expired_at' => $license->expires_at->format('Y-m-d'),
                ]));
                
                $this->info("Notified user {$user->id} about expired license {$license->license_key}.");
                Log::info("License expired notification sent", [
                    'user_id' => $user->id,
                    'license_id' => $license->id,
                ]);
            }
        }
    }
}
