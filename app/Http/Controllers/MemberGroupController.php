<?php

namespace App\Http\Controllers;

use App\Models\MemberGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MemberGroupController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:manage_members', ['only' => ['store', 'destroy']]);
    }

    public function index()
    {
        try {
            return Inertia::render('Members/Groups');
        } catch (\Exception $e) {
            Log::error('Failed to load groups page: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to load groups page'], 500);
        }
    }

    public function data(Request $request)
    {
        try {
            // Get the current authenticated user
            $user = auth()->user();
            Log::info('Loading groups for user: ' . $user->id);
            
            // Start with all groups
            $query = MemberGroup::query();
            
            // For non-admin users, filter groups based on who created them
            if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
                Log::info('User is not admin/super-admin, checking organization access');
                
                // Get the current user's organization ID
                $organizationId = $user->organization_id;
                Log::info('User organization ID: ' . ($organizationId ?? 'null'));
                
                // TEMPORARY FIX: Show all groups for regular users
                // This is a temporary fix until we properly implement organization-based filtering
                Log::info('TEMPORARY FIX: Showing all groups for regular users');
                
                // In the future, we'll implement proper organization-based filtering here
                // For now, we're not applying any filter to ensure users can see groups
            }
            
            $groups = $query->get()->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'member_count' => $group->participants()->count(),
                ];
            });
            
            Log::info('Found ' . $groups->count() . ' groups');
            return response()->json($groups);
        } catch (\Exception $e) {
            Log::error('Failed to load groups data: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to load groups data'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:member_groups,name'],
        ]);

        try {
            // Get the current authenticated user
            $user = auth()->user();
            
            $group = MemberGroup::create([
                'name' => $request->name,
                'created_by' => $user->id, // Set created_by to current user's ID
            ]);
            
            Log::info('Group created by user: ' . $user->id, ['group_id' => $group->id, 'group_name' => $group->name]);
            return response()->json(['message' => 'Group created', 'group' => $group], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create group: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to create group'], 500);
        }
    }

    public function destroy(MemberGroup $memberGroup)
    {
        if (! $memberGroup->exists) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        $memberGroup->participants()->detach();
        $memberGroup->delete();

        return response()->json(['message' => 'Group deleted'], 204);
    }
}
