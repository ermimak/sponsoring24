<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\License;
use App\Models\Donation;
use App\Models\BonusCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard index page.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get latest 10 notifications for the user
        $notifications = $user->notifications()->latest()->limit(10)->get();
        
        // Count unread notifications
        $unreadCount = $user->unreadNotifications()->count();
        
        // Get user's active license if any
        // Check if license is not null
        if ($user->licenses()->where('status', 'active')->where('expires_at', '>', now())->exists()) {
            $activeLicense = $user->licenses()->where('status', 'active')->where('expires_at', '>', now())->first();
        } else {
            $activeLicense = null;
        }
                
        $activeLicense = License::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();
        
        
        // Get user's projects count
        $projectsCount = Project::where('created_by', $user->id)->count();
        
        // Calculate projects growth
        $currentMonthProjects = Project::where('created_by', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
            
        $lastMonthProjects = Project::where('created_by', $user->id)
            ->where('created_at', '>=', now()->subMonth()->startOfMonth())
            ->where('created_at', '<', now()->startOfMonth())
            ->count();
            
        $projectsGrowth = 0;
        if ($lastMonthProjects > 0) {
            $projectsGrowth = round((($currentMonthProjects - $lastMonthProjects) / $lastMonthProjects) * 100);
        } elseif ($currentMonthProjects > 0) {
            $projectsGrowth = 100; // 100% growth if there were no projects last month but there are this month
        }
        
        // Get user's total donations received (if they have projects)
        $totalDonations = Donation::whereHas('project', function($query) use ($user) {
            $query->where('created_by', $user->id);
        })
            ->where('status', 'paid')
            ->sum('amount');
            
        // Calculate donations growth
        $currentMonthDonations = Donation::whereHas('project', function($query) use ($user) {
            $query->where('created_by', $user->id);
        })
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');
            
        $lastMonthDonations = Donation::whereHas('project', function($query) use ($user) {
            $query->where('created_by', $user->id);
        })
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonth()->startOfMonth())
            ->where('created_at', '<', now()->startOfMonth())
            ->sum('amount');
            
        $donationsGrowth = 0;
        if ($lastMonthDonations > 0) {
            $donationsGrowth = round((($currentMonthDonations - $lastMonthDonations) / $lastMonthDonations) * 100);
        }
            
        // Get referral stats
        $referralStats = [
            'totalReferrals' => BonusCredit::where('user_id', $user->id)
                ->where('type', 'referral')
                ->count(),
            'creditedReferrals' => BonusCredit::where('user_id', $user->id)
                ->where('type', 'referral')
                ->where('credited', true)
                ->count(),
            'pendingReferrals' => BonusCredit::where('user_id', $user->id)
                ->where('type', 'referral')
                ->where('credited', false)
                ->count(),
            'totalEarned' => BonusCredit::where('user_id', $user->id)
                ->where('type', 'referral')
                ->where('credited', true)
                ->sum('amount')
        ];
        
        // Calculate referrals growth
        $currentMonthReferrals = BonusCredit::where('user_id', $user->id)
            ->where('type', 'referral')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
            
        $lastMonthReferrals = BonusCredit::where('user_id', $user->id)
            ->where('type', 'referral')
            ->where('created_at', '>=', now()->subMonth()->startOfMonth())
            ->where('created_at', '<', now()->startOfMonth())
            ->count();
            
        $referralsGrowth = 0;
        if ($lastMonthReferrals > 0) {
            $referralsGrowth = round((($currentMonthReferrals - $lastMonthReferrals) / $lastMonthReferrals) * 100);
        } elseif ($currentMonthReferrals > 0) {
            $referralsGrowth = 100; // 100% growth if there were no referrals last month but there are this month
        }
        
        // Get recent activity
        $recentProjects = Project::where('created_by', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($project) {
                // Handle translatable fields (stored as JSON)
                $name = $project->name;
                if (is_string($name) && json_decode($name)) {
                    $nameData = json_decode($name, true);
                    // Use the first non-null translation or fallback to a placeholder
                    $name = collect($nameData)->filter()->first() ?? 'Untitled Project';
                }
                
                // Default status if not set
                $status = $project->status ?? 'active';
                
                return [
                    'id' => $project->id,
                    'name' => $name,
                    'status' => $status,
                    'updated_at' => $project->updated_at,
                    'image' => $project->image_square ?? $project->image_landscape ?? null,
                    'participants_count' => $project->participants()->count(),
                    'total_raised' => $project->participants()
                        ->withSum('donations as total_raised', 'amount')
                        ->get()
                        ->sum('total_raised') ?? 0
                ];
            });
            
        // Get new users who registered in the last 30 days
        $newUsersCount = User::where('created_at', '>=', now()->subDays(30))->count();
        $newUsersGrowth = 0;
        
        // Calculate growth percentage compared to previous 30 days
        $previousPeriodCount = User::where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->count();
            
        if ($previousPeriodCount > 0) {
            $newUsersGrowth = round((($newUsersCount - $previousPeriodCount) / $previousPeriodCount) * 100);
        }

        // Log data for debugging
        \Log::debug('Dashboard data', [
            'user_id' => $user->id,
            'projects_count' => $projectsCount,
            'total_donations' => $totalDonations,
            'recent_projects' => $recentProjects->toArray(),
            'referral_stats' => $referralStats
        ]);
        
        // Debug data being sent to the view
        $statsData = [
            'activeLicense' => $activeLicense ? [
                'key' => $activeLicense->license_key,
                'expires_at' => $activeLicense->expires_at,
                'days_remaining' => $activeLicense->expires_at ? now()->diffInDays($activeLicense->expires_at, false) : null
            ] : null,
            'projectsCount' => $projectsCount,
            'projectsGrowth' => $projectsGrowth,
            'totalDonations' => $totalDonations,
            'donationsGrowth' => $donationsGrowth,
            'referrals' => $referralStats,
            'referralsGrowth' => $referralsGrowth,
            'newUsersCount' => $newUsersCount,
            'newUsersGrowth' => $newUsersGrowth
        ];
        
        \Log::debug('Dashboard Stats Data:', $statsData);
        \Log::debug('Recent Projects:', $recentProjects->toArray());
        
        return Inertia::render('Dashboard/Index', [
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadCount,
            'stats' => $statsData,
            'recentProjects' => $recentProjects
        ]);
    }
}
