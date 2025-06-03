<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Project;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        // Get notifications for authenticated users
        $notifications = [];
        if ($request->user()) {
            $notifications = $request->user()->notifications()->latest()->take(10)->get();
        }
        
        // Get referral info if available
        $referralInfo = $request->session()->get('referralInfo');
        
        // Get featured projects (limit to 6)
        $projects = Project::where('public_donation_enabled', true)
            ->withCount(['participants', 'donations'])
            ->withSum('donations', 'amount')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->map(function ($project) {
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
                    'image' => $project->image_landscape ? '/storage/' . $project->image_landscape : '/images/project' . (($project->id % 3) + 1) . '.jpg',
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

        return Inertia::render('Welcome', [
            'notifications' => $notifications,
            'referralInfo' => $referralInfo,
            'projects' => $projects,
            'stats' => $stats
        ]);
    }
}