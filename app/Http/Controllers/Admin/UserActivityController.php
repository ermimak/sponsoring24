<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserActivityController extends Controller
{
    /**
     * Display a listing of all user activities.
     */
    public function index(Request $request)
    {
        $query = UserActivity::with('user')
            ->orderBy('created_at', 'desc');
            
        // Filter by activity type if provided
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->input('activity_type'));
        }
        
        // Filter by user if provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        
        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        
        $activities = $query->paginate(20)
            ->withQueryString();
            
        // Get unique activity types for filter dropdown
        $activityTypes = UserActivity::distinct()
            ->pluck('activity_type')
            ->toArray();
            
        return Inertia::render('Admin/UserActivities/Index', [
            'activities' => $activities,
            'activityTypes' => $activityTypes,
            'filters' => $request->only(['activity_type', 'user_id', 'date_from', 'date_to']),
        ]);
    }
    
    /**
     * Display activities for a specific user.
     */
    public function userActivities(Request $request, User $user)
    {
        $query = UserActivity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');
            
        // Filter by activity type if provided
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->input('activity_type'));
        }
        
        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        
        $activities = $query->paginate(20)
            ->withQueryString();
            
        // Get unique activity types for filter dropdown
        $activityTypes = UserActivity::where('user_id', $user->id)
            ->distinct()
            ->pluck('activity_type')
            ->toArray();
            
        return Inertia::render('Admin/UserActivities/UserActivities', [
            'user' => $user,
            'activities' => $activities,
            'activityTypes' => $activityTypes,
            'filters' => $request->only(['activity_type', 'date_from', 'date_to']),
        ]);
    }
    
    /**
     * Display details for a specific activity.
     */
    public function show(UserActivity $activity)
    {
        $activity->load('user');
        
        return Inertia::render('Admin/UserActivities/Show', [
            'activity' => $activity,
        ]);
    }
}
