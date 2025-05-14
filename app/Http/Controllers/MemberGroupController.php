<?php

namespace App\Http\Controllers;

use App\Models\MemberGroup;
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
            $groups = MemberGroup::all()->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'member_count' => $group->participants()->count(),
                ];
            });
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

        $group = MemberGroup::create(['name' => $request->name]);
        return response()->json(['message' => 'Group created', 'group' => $group], 201);
    }

    public function destroy(MemberGroup $memberGroup)
    {
        if (!$memberGroup->exists) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        $memberGroup->participants()->detach();
        $memberGroup->delete();
        return response()->json(['message' => 'Group deleted'], 204);
    }
}