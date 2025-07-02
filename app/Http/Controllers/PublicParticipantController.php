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
        try {
            // Use a database transaction for ACID compliance
            \DB::beginTransaction();
            
            $project = Project::findOrFail($projectId);
            
            // Check if the project allows public donations
            if (!$project->public_donation_enabled) {
                \DB::rollBack();
                \Log::warning('Attempted to donate to a project with public donations disabled', [
                    'project_id' => $projectId,
                    'participant_id' => $participantId,
                    'ip' => $request->ip(),
                ]);
                
                return response()->json([
                    'message' => 'This project does not allow public donations.'
                ], 403);
            }
            
            // Determine validation rules based on project settings
            $amountRule = 'required|numeric';
            if ($project->flat_rate_enabled && $project->flat_rate_min_amount > 0) {
                $amountRule .= '|min:' . $project->flat_rate_min_amount;
            } else {
                $amountRule .= '|min:1'; // Default minimum
            }
            
            // Validate the request with dynamic rules
            $validated = $request->validate([
                'amount' => $amountRule,
                'donor_name' => 'required|string|max:255',
                'donor_email' => 'required|email',
                'card_number' => 'required|string',
                'card_expiry' => 'required|string',
                'card_cvc' => 'required|string',
            ]);
            
            $participant = Participant::whereHas('projects', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })->findOrFail($participantId);

            // Create donation record with pending status initially
            $donation = Donation::create([
                'participant_id' => $participant->id,
                'project_id' => $project->id,
                'amount' => $request->amount,
                'donor_name' => $request->donor_name,
                'donor_email' => $request->donor_email,
                'status' => 'pending', // Start with pending status until payment is confirmed
                'currency' => 'CHF', // Default currency
                'payment_method' => 'card', // Default payment method
            ]);
            
            // Note: In a real implementation, we would create a Stripe payment intent here
            // and return it to the frontend for processing. After successful payment,
            // the webhook handler would update the donation status to 'completed'.
            
            // For mock implementation, we'll simulate a successful payment
            $donation->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);
            
            \DB::commit();
            
            // Send notifications outside of the transaction to avoid blocking
            $this->sendDonationNotifications($donation, $project, $participant);
            
            return response()->json([
                'message' => 'Donation successful', 
                'data' => $donation
            ], 201);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            
            \Log::error('Error processing donation', [
                'project_id' => $projectId,
                'participant_id' => $participantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'An error occurred while processing your donation', 
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send notifications for a successful donation
     * 
     * @param Donation $donation
     * @param Project $project
     * @param Participant $participant
     * @return void
     */
    private function sendDonationNotifications($donation, $project, $participant)
    {
        try {
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
            
            \Log::info('Donation notifications sent', [
                'donation_id' => $donation->id,
                'project_id' => $donation->project_id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending donation notifications', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}