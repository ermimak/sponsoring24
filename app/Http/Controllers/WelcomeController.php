<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\News;
use App\Models\Project;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        // Get notifications for authenticated users
        $notifications = [];
        $unreadCount = 0;
        $projects = [];
        $stats = [];
        $news = [];
        if ($request->user()) {
            $notifications = $request->user()->notifications()->latest()->take(10)->get();
            $unreadCount = $request->user()->unreadNotifications()->count();
        }
        
        // Get referral info if available
        $referralInfo = $request->session()->get('referralInfo');

        // Check if Project table exists
        if (!Schema::hasTable('projects')) {
            return Inertia::render('Welcome', [
                'notifications' => $notifications,
                'unreadNotificationsCount' => $unreadCount,
                'referralInfo' => $referralInfo,
                'projects' => $projects,
                'stats' => $stats,
                'news' => $news
            ]);
        }

        // Check if there is no project
        if (Project::count() == 0) {
            $projects = collect([]);
        } else {
            $projects = Project::where('is_featured', true)
            ->where('public_donation_enabled', true)
            ->withCount(['participants', 'donations'])
            ->withSum('donations', 'amount')
            ->orderBy('featured_order')
            ->take(6)
            ->get();
        }    
        // If no featured projects, get the most recent ones
        if (is_null($projects)) {
            $projects = Project::where('public_donation_enabled', true)
                ->withCount(['participants', 'donations'])
                ->withSum('donations', 'amount')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
        }
        
        // Format project data for display
        // Call to a member function map() on array
        if (!is_array($projects)) {
            $projects = collect($projects);
        }
        $projects = $projects->map(function ($project) {
                // Handle translatable fields
                $name = $project->name;
                if (is_array($name)) {
                    $name = $name[$project->language] ?? reset($name);
                }
                
                $description = $project->description;
                if (is_array($description)) {
                    $description = $description[$project->language] ?? reset($description);
                }
                
                // Format the total donations amount
                $totalDonations = $project->donations_sum_amount ?? 0;
                $formattedAmount = 'CHF ' . number_format($totalDonations, 0, '.', ',');
                
                return [
                    'id' => $project->id,
                    'title' => $name,
                    'description' => $description,
                    'image' => $project->image_landscape ? $project->image_landscape : $project->image_square,
                    'raised' => $formattedAmount,
                    'participants_count' => $project->participants_count,
                    'donations_count' => $project->donations_count,
                ];
            });
            
        // Get statistics
        $stats = [
            'projects' => Project::count(),
            'donations' => Donation::sum('amount'),
            'supporters' => DB::table('donations')->distinct('supporter_email')->count('supporter_email')
        ];

        // map news to return 4 items only
        $news = News::where('is_published', true)->take(3)->get()->map(function ($news) {
            return [
                'id' => $news->id,
                'title' => $news->title,
                'content' => $news->content,
                'excerpt' => $news->excerpt,
                'image_url' => $news->image_url ? '/storage/'.$news->image_url : null,
                'is_published' => $news->is_published,
            ];
        });

        return Inertia::render('Welcome', [
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadCount,
            'referralInfo' => $referralInfo,
            'projects' => $projects,
            'stats' => $stats,
            'news' => $news
        ]);
    }
}