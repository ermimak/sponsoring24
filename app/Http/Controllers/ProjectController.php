<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Participant;
use App\Models\Donation;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return Project::withCount(['participants', 'donations'])
            ->withSum('donations', 'amount')
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|array',
            'description' => 'nullable|array',
            'location' => 'nullable|string',
            'language' => 'required|string|in:de,fr',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'allow_donation_until' => 'nullable|date|after:end',
            'image_landscape' => 'nullable|string',
            'image_square' => 'nullable|string',
            'flat_rate_enabled' => 'boolean',
            'flat_rate_min_amount' => 'nullable|numeric|min:0',
            'flat_rate_help_text' => 'nullable|string',
            'unit_based_enabled' => 'boolean',
            'public_donation_enabled' => 'boolean',
            'created_by' => 'exists:users,id',
        ]);

        $data['created_by'] = $data['created_by'] ?? auth()->id();
        $project = Project::create($data);
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

        $data = $request->validate([
            'name' => 'required|array',
            'description' => 'nullable|array',
            'location' => 'nullable|string',
            'language' => 'required|string|in:de,fr',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'allow_donation_until' => 'nullable|date|after:end',
            'flat_rate_enabled' => 'boolean',
            'flat_rate_min_amount' => 'nullable|numeric|min:0',
            'flat_rate_help_text' => 'nullable|string',
            'unit_based_enabled' => 'boolean',
            'public_donation_enabled' => 'boolean',
            'image_landscape' => 'nullable|image|max:2048|dimensions:width=1200,height=300',
            'image_square' => 'nullable|image|max:2048|dimensions:width=400,height=400',
        ]);

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

        $project->delete();
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
            'image_landscape' => 'nullable|image|max:2048|dimensions:width=1200,height=300',
            'image_square' => 'nullable|image|max:2048|dimensions:width=400,height=400',
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