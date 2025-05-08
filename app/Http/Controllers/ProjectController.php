<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Participant;
use App\Models\Donation;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return Project::withCount(['participants', 'donations'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|array',
            'description' => 'nullable|array',
            'location' => 'nullable|string',
            'language' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'allow_donation_until' => 'nullable|date',
            'image_landscape' => 'nullable|string',
            'image_square' => 'nullable|string',
            'flat_rate_enabled' => 'boolean',
            'flat_rate_min_amount' => 'nullable|numeric',
            'flat_rate_help_text' => 'nullable|string',
            'unit_based_enabled' => 'boolean',
            'public_donation_enabled' => 'boolean',
            'created_by' => 'nullable|exists:users,id',
        ]);
        $project = Project::create($data);
        return $project;
    }

    public function show(Project $project)
    {
        return $project->load(['participants', 'donations', 'emailTemplates']);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|array',
            'description' => 'nullable|array',
            'location' => 'nullable|string',
            'language' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'allow_donation_until' => 'nullable|date',
            'image_landscape' => 'nullable|string',
            'image_square' => 'nullable|string',
            'flat_rate_enabled' => 'boolean',
            'flat_rate_min_amount' => 'nullable|numeric',
            'flat_rate_help_text' => 'nullable|string',
            'unit_based_enabled' => 'boolean',
            'public_donation_enabled' => 'boolean',
        ]);
        $project->update($data);
        return $project;
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->noContent();
    }

    public function addParticipant(Request $request, Project $project)
    {
        $data = $request->validate(['participant_id' => 'required|exists:participants,id', 'status' => 'nullable|string', 'role' => 'nullable|string']);
        $project->participants()->attach($data['participant_id'], ['status' => $data['status'] ?? null, 'role' => $data['role'] ?? null]);
        return response()->noContent();
    }

    public function removeParticipant(Request $request, Project $project)
    {
        $data = $request->validate(['participant_id' => 'required|exists:participants,id']);
        $project->participants()->detach($data['participant_id']);
        return response()->noContent();
    }

    public function uploadImage(Request $request, Project $project)
    {
        $request->validate([
            'image_landscape' => 'nullable|image|max:2048',
            'image_square' => 'nullable|image|max:2048',
        ]);
        $data = [];
        if ($request->hasFile('image_landscape')) {
            $path = $request->file('image_landscape')->store('projects/landscape', 'public');
            $project->image_landscape = $path;
            $data['image_landscape'] = Storage::disk('public')->url($path);
        }
        if ($request->hasFile('image_square')) {
            $path = $request->file('image_square')->store('projects/square', 'public');
            $project->image_square = $path;
            $data['image_square'] = Storage::disk('public')->url($path);
        }
        $project->save();
        return response()->json($data);
    }
} 