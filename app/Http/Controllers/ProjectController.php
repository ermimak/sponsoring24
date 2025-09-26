<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectUpdateNotification;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // Get projects that are created by the current user or are public
        return Project::withCount(['participants', 'donations'])
            ->withSum('donations', 'amount')
            ->withSum(['donations as paid_donations_sum_amount' => function($query) {
                $query->where('status', 'paid');
            }], 'amount')
            ->where('created_by', Auth::id())
            ->paginate(20);
    }

    public function store(Request $request)
    {
        // Basic validation for all projects
        $rules = [
            'name' => 'required|array',
            'description' => 'nullable|array',
            'location' => 'nullable|string',
            'language' => 'required|string|in:de,fr',
            'start' => 'required|date|after_or_equal:today',
            'end' => 'required|date|after:start',
            'allow_donation_until' => 'nullable|date|after:end',
            'image_landscape' => 'nullable|string',
            'image_square' => 'nullable|string',
            'flat_rate_enabled' => 'boolean',
            'unit_based_enabled' => 'boolean',
            'public_donation_enabled' => 'boolean',
            'created_by' => 'exists:users,id',
        ];
        
        // Add conditional validation rules for flat-rate donations
        if ($request->input('flat_rate_enabled')) {
            $rules['flat_rate_min_amount'] = 'required|numeric|min:0';
            $rules['flat_rate_help_text'] = 'nullable|string';
        } else {
            $rules['flat_rate_min_amount'] = 'nullable|numeric|min:0';
            $rules['flat_rate_help_text'] = 'nullable|string';
        }
        
        $data = $request->validate($rules);

        // Set default values for donation options if not provided
        $data['flat_rate_enabled'] = $data['flat_rate_enabled'] ?? false;
        $data['unit_based_enabled'] = $data['unit_based_enabled'] ?? false;
        $data['public_donation_enabled'] = $data['public_donation_enabled'] ?? false;
        
        // If flat rate is disabled, ensure min amount and help text are null
        if (!$data['flat_rate_enabled']) {
            $data['flat_rate_min_amount'] = null;
            $data['flat_rate_help_text'] = null;
        }
        
        $data['created_by'] = $data['created_by'] ?? auth()->id();
        $project = Project::create($data);
        
        // Log project creation activity
        UserActivityService::logProject('project_created', Auth::id(), [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'language' => $project->language,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        // Send notification to admin users about new project
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new ProjectUpdateNotification($project, 'created'));
        }

        return $project;
    }

    public function show(Project $project)
    {
        if (auth()->check() && ($project->created_by === null || $project->created_by === auth()->id())) {
            return Inertia::render('Projects/Edit', [
                'project' => $project->load(['participants', 'donations', 'emailTemplates']),
                'projectId' => $project->id,
            ]);
        }

        abort(403, 'Unauthorized');
    }

    public function update(Request $request, Project $project)
    {
        if ($project->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Basic validation for all projects
        $rules = [
            'name' => 'required|array',
            'description' => 'nullable|array',
            'location' => 'nullable|string',
            'language' => 'required|string|in:de,fr',
            'start' => 'required|date|after_or_equal:today',
            'end' => 'required|date|after:start',
            'allow_donation_until' => 'nullable|date|after:end',
            'flat_rate_enabled' => 'boolean',
            'unit_based_enabled' => 'boolean',
            'public_donation_enabled' => 'boolean',
            'image_landscape' => 'nullable|image|max:6144|dimensions:width=1200,height=300',
            'image_square' => 'nullable|image|max:6144|dimensions:width=400,height=400',
        ];
        
        // Add conditional validation rules for flat-rate donations
        if ($request->input('flat_rate_enabled')) {
            $rules['flat_rate_min_amount'] = 'required|numeric|min:0';
            $rules['flat_rate_help_text'] = 'nullable|string';
        } else {
            $rules['flat_rate_min_amount'] = 'nullable|numeric|min:0';
            $rules['flat_rate_help_text'] = 'nullable|string';
        }
        
        $data = $request->validate($rules);
        
        // Set default values for donation options if not provided
        $data['flat_rate_enabled'] = $data['flat_rate_enabled'] ?? false;
        $data['unit_based_enabled'] = $data['unit_based_enabled'] ?? false;
        $data['public_donation_enabled'] = $data['public_donation_enabled'] ?? false;
        
        // If flat rate is disabled, ensure min amount and help text are null
        if (!$data['flat_rate_enabled']) {
            $data['flat_rate_min_amount'] = null;
            $data['flat_rate_help_text'] = null;
        }

        if ($request->hasFile('image_landscape')) {
            if ($project->image_landscape) {
                Storage::disk('public')->delete($project->image_landscape);
            }
            $path = $request->file('image_landscape')->store('projects/landscape', 'public');
            $data['image_landscape'] = $path;
        }

        if ($request->hasFile('image_square')) {
            if ($project->image_square) {
                Storage::disk('public')->delete($project->image_square);
            }
            $path = $request->file('image_square')->store('projects/square', 'public');
            $data['image_square'] = $path;
        }

        $project->update($data);
        
        // Log project update activity
        UserActivityService::logProject('project_updated', Auth::id(), [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'language' => $project->language,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'updated_fields' => array_keys($data)
        ]);
        
        // Send notification to admin users about project update
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new ProjectUpdateNotification($project, 'updated', [
                'updated_fields' => array_keys($data),
                'updated_by' => auth()->user()->name
            ]));
        }

        return response()->json([
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'location' => $project->location,
            'language' => $project->language,
            'start' => $project->start,
            'end' => $project->end,
            'allow_donation_until' => $project->allow_donation_until,
            'flat_rate_enabled' => $project->flat_rate_enabled,
            'flat_rate_min_amount' => $project->flat_rate_min_amount,
            'flat_rate_help_text' => $project->flat_rate_help_text,
            'unit_based_enabled' => $project->unit_based_enabled,
            'public_donation_enabled' => $project->public_donation_enabled,
            'image_landscape' => $project->image_landscape ? Storage::disk('public')->url($project->image_landscape) : null,
            'image_square' => $project->image_square ? Storage::disk('public')->url($project->image_square) : null,
        ]);
    }

    public function destroy(Project $project)
    {
        if ($project->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($project->image_landscape) {
            Storage::disk('public')->delete($project->image_landscape);
        }
        if ($project->image_square) {
            Storage::disk('public')->delete($project->image_square);
        }

        // Store project info before deletion for logging
        $projectInfo = [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'language' => $project->language,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];
        
        // Send notification to admin users about project deletion
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();
        
        $projectCopy = clone $project; // Create a copy before deletion for notification
        
        $project->delete();
        
        // Log project deletion activity
        UserActivityService::logProject('project_deleted', Auth::id(), $projectInfo);
        
        foreach ($admins as $admin) {
            $admin->notify(new ProjectUpdateNotification($projectCopy, 'deleted', [
                'deleted_by' => auth()->user()->name,
                'deleted_at' => now()->toDateTimeString()
            ]));
        }

        return response()->noContent();
    }

    public function duplicate(Project $project)
    {
        if ($project->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $newProject = $project->replicate();
        $newProject->name = array_map(function ($name) {
            return $name . ' (Copy)';
        }, $newProject->name);
        $newProject->created_by = auth()->id();
        $newProject->save();

        foreach ($project->emailTemplates as $template) {
            $newTemplate = $template->replicate();
            $newTemplate->project_id = $newProject->id;
            $newTemplate->save();
        }
        
        // Log project duplication activity
        UserActivityService::logProject('project_duplicated', Auth::id(), [
            'original_project_id' => $project->id,
            'original_project_name' => $project->name,
            'new_project_id' => $newProject->id,
            'new_project_name' => $newProject->name,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        // Send notification to admin users about project duplication
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new ProjectUpdateNotification($newProject, 'duplicated', [
                'original_project_id' => $project->id,
                'original_project_name' => $project->name,
                'duplicated_by' => auth()->user()->name
            ]));
        }

        return $newProject;
    }

    public function addParticipant(Request $request, Project $project)
    {
        if ($project->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'status' => 'nullable|string',
            'role' => 'nullable|string',
        ]);
        $project->participants()->attach($data['participant_id'], [
            'status' => $data['status'] ?? null,
            'role' => $data['role'] ?? null,
        ]);

        return response()->noContent();
    }

    public function removeParticipant(Request $request, Project $project)
    {
        if ($project->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'participant_id' => 'required|exists:participants,id',
        ]);
        $project->participants()->detach($data['participant_id']);

        return response()->noContent();
    }

    public function uploadImage(Request $request, Project $project)
    {
        if ($project->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'image_landscape' => 'nullable|image|max:6144|dimensions:width=1200,height=300',
            'image_square' => 'nullable|image|max:6144|dimensions:width=400,height=400',
        ]);

        $data = [];
        if ($request->hasFile('image_landscape')) {
            if ($project->image_landscape) {
                Storage::disk('public')->delete($project->image_landscape);
            }
            $path = $request->file('image_landscape')->store('projects/landscape', 'public');
            $project->image_landscape = $path;
            $data['image_landscape'] = Storage::disk('public')->url($path);
        }
        if ($request->hasFile('image_square')) {
            if ($project->image_square) {
                Storage::disk('public')->delete($project->image_square);
            }
            $path = $request->file('image_square')->store('projects/square', 'public');
            $project->image_square = $path;
            $data['image_square'] = Storage::disk('public')->url($path);
        }
        $project->save();

        return response()->json($data);
    }
}
