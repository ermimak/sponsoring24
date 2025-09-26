<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ValidateParticipantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $projectId = $request->route('projectId');
        $participantId = $request->route('participantId');

        // Rate limiting per IP for public routes
        $key = 'participant-access:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) { // 60 requests per minute per IP
            Log::warning('Rate limit exceeded for participant access', [
                'ip' => $request->ip(),
                'project_id' => $projectId,
                'participant_id' => $participantId,
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json([
                'error' => 'Too many requests. Please try again later.'
            ], 429);
        }

        RateLimiter::hit($key, 60); // 1 minute decay

        // Validate that project exists and donations are allowed
        try {
            $project = Project::findOrFail($projectId);
            
            // Check if donations are still allowed based on allow_donation_until date
            if ($project->allow_donation_until && $project->allow_donation_until->isPast()) {
                Log::info('Access attempt to project with expired donation period', [
                    'project_id' => $projectId,
                    'allow_donation_until' => $project->allow_donation_until,
                    'ip' => $request->ip()
                ]);
                
                return response()->view('errors.project-expired', [
                    'message' => 'The donation period for this project has ended.'
                ], 403);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Access attempt to non-existent project', [
                'project_id' => $projectId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->view('errors.404', [
                'message' => 'Project not found.'
            ], 404);
        }

        // Validate that participant exists and belongs to the project
        try {
            $participant = Participant::whereHas('projects', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })->findOrFail($participantId);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Access attempt to invalid participant', [
                'project_id' => $projectId,
                'participant_id' => $participantId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->view('errors.404', [
                'message' => 'Participant not found in this project.'
            ], 404);
        }

        // Log legitimate access for audit trail
        Log::info('Valid participant page access', [
            'project_id' => $projectId,
            'participant_id' => $participantId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route' => $request->route()->getName()
        ]);

        return $next($request);
    }
}
