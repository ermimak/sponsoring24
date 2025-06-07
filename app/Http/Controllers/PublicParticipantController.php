<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Participant;
use App\Models\Project;
use App\Models\User;
use App\Notifications\NewDonationNotification;
use Illuminate\Http\Request;

class PublicParticipantController extends Controller
{
    public function show($projectId, $participantId)
    {
        $project = Project::findOrFail($projectId);
        $participant = Participant::whereHas('projects', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->findOrFail($participantId);

        $participant->total_donations = Donation::where('participant_id', $participantId)
            ->where('project_id', $projectId)
            ->sum('amount');

        return response()->json(['data' => $participant]);
    }

    public function donate(Request $request, $projectId, $participantId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email',
            'card_number' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvc' => 'required|string',
        ]);

        $project = Project::findOrFail($projectId);
        $participant = Participant::whereHas('projects', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->findOrFail($participantId);

        // Mock payment processing (replace with actual payment gateway like Stripe)
        $donation = Donation::create([
            'participant_id' => $participant->id,
            'project_id' => $project->id,
            'amount' => $request->amount,
            'donor_name' => $request->donor_name,
            'donor_email' => $request->donor_email,
            'status' => 'completed',
        ]);

        // Send notification to project owner
        $projectOwner = User::find($project->created_by);
        if ($projectOwner) {
            $projectOwner->notify(new NewDonationNotification($donation, $project, $participant));
        }
        
        // Send notification to admin users
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new NewDonationNotification($donation, $project, $participant));
        }

        return response()->json(['message' => 'Donation successful', 'data' => $donation], 201);
    }
}
