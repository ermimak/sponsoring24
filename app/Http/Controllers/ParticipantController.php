<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipantRequest;
use App\Http\Requests\UpdateParticipantRequest;
use App\Imports\ParticipantsImport;
use App\Models\Donation;
use App\Models\EmailTemplate;
use App\Models\MemberGroup;
use App\Models\Participant;
use App\Models\ParticipantProject;
use App\Models\Project;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ParticipantController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:manage_members', ['only' => ['store', 'update', 'destroy']]);
    }

    public function index(Request $request, $projectId)
    {
        try {
            // Verify project exists
            $project = Project::findOrFail($projectId);

            // Fetch participants via pivot model
            $participantProjects = ParticipantProject::where('project_id', $projectId)
                ->with(['participant.memberGroups', 'participant.donations'])
                ->get();

            // Log the raw results
            Log::info('ParticipantProject raw results:', [
                'count' => $participantProjects->count(),
                'data' => $participantProjects->toArray(),
            ]);

            $participants = $participantProjects->map(function ($participantProject) {
                $participant = $participantProject->participant;
                if (! $participant) {
                    return null;
                }

                try {
                    return [
                        'id' => $participant->id,
                        'first_name' => $participant->first_name,
                        'last_name' => $participant->last_name,
                        'email' => $participant->email,
                        'member_groups' => $participant->memberGroups->map(function ($group) {
                            return ['id' => $group->id, 'name' => $group->name];
                        })->toArray(),
                        'supporters' => $participant->supporters ?? 0,
                        'sales_volume' => $participant->sales_volume ?? 0,
                        'emails' => $participant->emails_sent ?? 0,
                        'landing_page_opened' => $participant->landing_page_opened ?? false,
                    ];
                } catch (\Exception $e) {
                    Log::error('Failed to map participant ' . $participant->id . ': ' . $e->getMessage());

                    return null;
                }
            })->filter();

            // Log the final mapped results
            Log::info('Mapped participants:', [
                'count' => $participants->count(),
                'data' => $participants->toArray(),
            ]);

            return response()->json(['data' => $participants]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Project not found: ' . $projectId);

            return response()->json(['error' => 'Project not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to load participants for project ' . $projectId . ': ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to load participants: ' . $e->getMessage()], 500);
        }
    }

    public function addToProject(Request $request, $projectId)
    {
        $request->validate([
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:member_groups,id',
        ]);

        try {
            $project = Project::findOrFail($projectId);
            $participants = Participant::whereHas('memberGroups', function ($query) use ($request) {
                $query->whereIn('member_group_id', $request->group_ids);
            })->get();

            foreach ($participants as $participant) {
                $participant->projects()->syncWithoutDetaching([
                    $project->id => ['status' => 'active', 'role' => 'participant'],
                ]);
            }

            // Return the updated list of participants for the project
            $updatedParticipants = Participant::whereHas('projects', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })->with(['memberGroups', 'donations'])->get()->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'first_name' => $participant->first_name,
                    'last_name' => $participant->last_name,
                    'email' => $participant->email,
                    'member_groups' => $participant->memberGroups->map(function ($group) {
                        return ['id' => $group->id, 'name' => $group->name];
                    }),
                    'supporters' => $participant->supporters,
                    'sales_volume' => $participant->sales_volume,
                    'emails' => $participant->emails_sent,
                    'landing_page_opened' => $participant->landing_page_opened,
                ];
            });

            return response()->json(['message' => 'Participants added to project', 'data' => $updatedParticipants]);
        } catch (\Exception $e) {
            Log::error('Failed to add participants to project ' . $projectId . ': ' . $e->getMessage());

            return response()->json(['error' => 'Failed to add participants'], 500);
        }
    }

    public function sendEmail(Request $request, $participantId)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'subject' => 'required|string',
            'body' => 'required|string',
            'project_id' => 'required|exists:projects,id',
        ]);

        try {
            $participant = Participant::findOrFail($participantId);
            $template = EmailTemplate::findOrFail($request->template_id);
            $project = Project::findOrFail($request->project_id);
            
            $emailService = new EmailService();
            $success = $emailService->sendToParticipant(
                $participant,
                $template,
                $request->subject,
                $request->body,
                ['project_id' => $project->id]
            );

            if ($success) {
                return response()->json(['message' => 'Email sent successfully']);
            } else {
                return response()->json(['error' => 'Failed to send email'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email to participant ' . $participantId . ': ' . $e->getMessage());

            return response()->json(['error' => 'Failed to send email'], 500);
        }
    }

    public function sendMassEmail(Request $request, $projectId)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        try {
            $project = Project::findOrFail($projectId);
            $template = EmailTemplate::findOrFail($request->template_id);
            
            $emailService = new EmailService();
            $results = $emailService->sendMassEmailToProjectParticipants(
                $project,
                $template,
                $request->subject,
                $request->body
            );

            return response()->json([
                'message' => 'Mass email sent successfully',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send mass email for project ' . $projectId . ': ' . $e->getMessage());

            return response()->json(['error' => 'Failed to send mass email'], 500);
        }
    }

    public function indexAll(Request $request)
    {
        try {
            $query = Participant::with('memberGroups');
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'ilike', "%{$search}%")
                      ->orWhere('last_name', 'ilike', "%{$search}%")
                      ->orWhere('member_id', 'ilike', "%{$search}%")
                      ->orWhere('email', 'ilike', "%{$search}%")
                      ->orWhere('company', 'ilike', "%{$search}%")
                      ->orWhere('location', 'ilike', "%{$search}%")
                      ->orWhere('postal_code', 'ilike', "%{$search}%")
                      ->orWhere('phone', 'ilike', "%{$search}%");
                });
            }
            $members = $query->get()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'first_name' => $m->first_name,
                    'last_name' => $m->last_name,
                    'member_id' => $m->member_id,
                    'email' => $m->email,
                    'email_cc' => $m->email_cc,
                    'phone' => $m->phone,
                    'gender' => $m->gender,
                    'company' => $m->company,
                    'address' => $m->address,
                    'address_suffix' => $m->address_suffix,
                    'postal_code' => $m->postal_code,
                    'location' => $m->location,
                    'country' => $m->country,
                    'birthday' => $m->birthday ? Carbon::parse($m->birthday)->toDateString() : '',
                    'email_status' => $m->email_status ?? 'OK',
                    'public_registration' => (bool) $m->public_registration,
                    'archived' => (bool) $m->archived,
                    'created_at' => $m->created_at?->toDateTimeString(),
                    'member_groups' => $m->memberGroups->map(function ($g) {
                        return [
                            'id' => $g->id,
                            'name' => $g->name,
                        ];
                    })->toArray(),
                ];
            })->toArray();

            if ($request->wantsJson() || $request->isXmlHttpRequest()) {
                return response()->json($members);
            }

            return Inertia::render('Members/Index', ['members' => $members]);
        } catch (\Exception $e) {
            Log::error('Failed to load members: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to load members'], 500);
        }
    }

    public function store(StoreParticipantRequest $request)
    {
        try {
            $participant = Participant::create($request->validated());
            if ($request->has('groups')) {
                $groupIds = MemberGroup::whereIn('name', $request->input('groups'))->pluck('id');
                $participant->memberGroups()->sync($groupIds);
            }

            return response()->json(['message' => 'Participant created', 'participant' => $participant], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create participant: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to create participant'], 500);
        }
    }

    public function show(Participant $participant)
    {
        if (! $participant->exists) {
            return response()->json(['message' => 'Participant not found'], 404);
        }
        $participant->load('memberGroups');

        return response()->json([
            'id' => $participant->id,
            'gender' => $participant->gender ?? '',
            'first_name' => $participant->first_name ?? '',
            'last_name' => $participant->last_name ?? '',
            'company' => $participant->company ?? '',
            'address' => $participant->address ?? '',
            'address_suffix' => $participant->address_suffix ?? '',
            'postal_code' => $participant->postal_code ?? '',
            'location' => $participant->location ?? '',
            'country' => $participant->country ?? '',
            'birthday' => $participant->birthday ? Carbon::parse($participant->birthday)->toDateString() : '',
            'member_id' => $participant->member_id ?? '',
            'email' => $participant->email ?? '',
            'email_cc' => $participant->email_cc ?? '',
            'phone' => $participant->phone ?? '',
            'public_registration' => (bool) $participant->public_registration,
            'archived' => (bool) $participant->archived,
            'email_status' => $participant->email_status ?? 'OK',
            'created_at' => $participant->created_at?->toDateTimeString(),
            'member_groups' => $participant->memberGroups->map(function ($g) {
                return ['id' => $g->id, 'name' => $g->name];
            })->toArray(),
        ]);
    }

    public function update(UpdateParticipantRequest $request, Participant $participant)
    {
        if (! $participant->exists) {
            return response()->json(['message' => 'Participant not found'], 404);
        }

        try {
            $data = array_filter($request->validated(), fn ($value) => ! is_null($value));
            $participant->update($data);

            if ($request->has('groups')) {
                $groupNames = array_filter($request->input('groups'), fn ($name) => ! empty(trim($name)));
                if (! empty($groupNames)) {
                    $groupIds = collect($groupNames)->map(function ($name) {
                        return MemberGroup::firstOrCreate(['name' => trim($name)])->id;
                    })->toArray();
                    $participant->memberGroups()->sync($groupIds);
                } else {
                    $participant->memberGroups()->detach();
                }
            }

            return response()->json(['message' => 'Participant updated', 'participant' => $participant->load('memberGroups')]);
        } catch (\Exception $e) {
            Log::error('Failed to update participant ' . $participant->id . ': ' . $e->getMessage());

            return response()->json(['error' => 'Failed to update participant'], 500);
        }
    }

    public function destroy(Participant $participant)
    {
        if (! $participant->exists) {
            return response()->json(['message' => 'Participant not found'], 404);
        }

        try {
            $participant->memberGroups()->detach();
            $participant->projects()->detach();
            $participant->delete();

            return response()->json(['message' => 'Participant deleted'], 204);
        } catch (\Exception $e) {
            Log::error('Failed to delete participant ' . $participant->id . ': ' . $e->getMessage());

            return response()->json(['error' => 'Failed to delete participant'], 500);
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => ['required', 'file', 'mimes:csv,xlsx'],
            ]);

            $path = $request->file('file')->store('imports', 'local');
            $fullPath = Storage::disk('local')->path($path);

            Excel::import(new ParticipantsImport, $fullPath);

            Storage::disk('local')->delete($path);

            return response()->json(['message' => 'Import successful']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->errors()['import'][0] ?? 'Invalid file format'], 422);
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());

            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 422);
        }
    }

    public function export(Request $request, $projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $participants = Participant::whereHas('projects', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })->with(['memberGroups'])->get();

            $export = new \App\Exports\ProjectParticipantsExport($participants);

            return Excel::download($export, "participants_project_{$projectId}.csv");
        } catch (\Exception $e) {
            Log::error('Export failed for project ' . $projectId . ': ' . $e->getMessage());

            return response()->json(['message' => 'Export failed: ' . $e->getMessage()], 422);
        }
    }

    public function showDonationPage(Request $request, $projectId, $participantId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $participant = Participant::whereHas('projects', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })->findOrFail($participantId);

            $participantData = [
                'id' => $participant->id,
                'first_name' => $participant->first_name,
                'last_name' => $participant->last_name,
                'sales_volume' => $participant->sales_volume ?? 0,
            ];

            // Handle translatable fields
            $name = $project->name ?? '';
            $description = $project->description ?? '';
            if (is_array($name)) {
                $name = reset($name); // Use the first translation as default
            }
            if (is_array($description)) {
                $description = reset($description); // Use the first translation as default
            }

            // Split start datetime into date and time
            $startDateTime = $project->start;
            $date = $startDateTime ? $startDateTime->format('F d, Y') : null;
            $time = $startDateTime ? $startDateTime->format('H:i') : null;

            return Inertia::render('Projects/Participants/Donate', [
                'project' => [
                    'id' => $project->id,
                    'name' => $name,
                    'description' => $description,
                    'date' => $date,
                    'time' => $time,
                    'location' => $project->location,
                    'image_url' => $project->image_landscape, // Use image_landscape as the landing image
                ],
                'participant' => $participantData,
                'step' => 'donation',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Resource not found: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Project or Participant not found.');
        } catch (\Exception $e) {
            Log::error('Failed to load donation page: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to load donation page.');
        }
    }

    public function storeDonation(Request $request, $projectId, $participantId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $participant = Participant::whereHas('projects', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })->findOrFail($participantId);

            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'currency' => 'required|string|in:USD,EUR,GBP,CHF',
                'step' => 'required|in:donation,confirmation',
            ]);

            if ($validated['step'] === 'donation') {
                $request->session()->put('donation_data', [
                    'amount' => $validated['amount'],
                    'currency' => $validated['currency'],
                    'project_id' => $projectId,
                    'participant_id' => $participantId,
                ]);

                // Handle translatable fields for rendering the next step
                $name = $project->name ?? '';
                $description = $project->description ?? '';
                if (is_array($name)) {
                    $name = reset($name); // Use the first translation as default
                }
                if (is_array($description)) {
                    $description = reset($description); // Use the first translation as default
                }

                $startDateTime = $project->start;
                $date = $startDateTime ? $startDateTime->format('F d, Y') : null;
                $time = $startDateTime ? $startDateTime->format('H:i') : null;

                // Render the same page but with step set to 'confirmation'
                return Inertia::render('Projects/Participants/Donate', [
                    'project' => [
                        'id' => $project->id,
                        'name' => $name,
                        'description' => $description,
                        'date' => $date,
                        'time' => $time,
                        'location' => $project->location,
                        'image_url' => $project->image_landscape, // Use image_landscape as the landing image
                    ],
                    'participant' => [
                         'id' => $participant->id,
                         'first_name' => $participant->first_name,
                         'last_name' => $participant->last_name,
                         'sales_volume' => $participant->sales_volume ?? 0,
                    ],
                    'step' => 'confirmation',
                    'form' => [ // Pass the collected amount to the next step form
                        'amount' => $validated['amount'],
                        'currency' => $validated['currency'],
                         // include other default values for the next step form fields if needed
                         'gender' => 'Masculine',
                         'first_name' => '',
                         'last_name' => '',
                         'company' => '',
                         'address' => '',
                         'address_suffix' => '',
                         'postal_code' => '',
                         'location' => '',
                         'country' => 'Switzerland',
                         'email' => '',
                         'phone' => '',
                         'privacy_policy' => false,
                    ],
                ]);
            } elseif ($validated['step'] === 'confirmation') {
                $donationData = $request->session()->get('donation_data', []);
                if (empty($donationData)) {
                    return redirect()->back()->with('error', 'No donation data found.');
                }

                $personalData = $request->validate([
                    'gender' => 'required|string',
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'company' => 'nullable|string|max:255',
                    'address' => 'required|string|max:255',
                    'address_suffix' => 'nullable|string|max:255',
                    'postal_code' => 'required|string|max:20',
                    'location' => 'required|string|max:255',
                    'country' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phone' => 'required|string|max:20',
                    'privacy_policy' => 'required|accepted',
                ]);

                $email = $personalData['email'];

                // Find or create the supporter based on email
                $supporter = \App\Models\Supporter::firstOrCreate(
                    ['email' => $email],  // Search criteria
                    [
                        'gender' => $personalData['gender'] ?? null,
                        'first_name' => $personalData['first_name'],
                        'last_name' => $personalData['last_name'],
                        'company' => $personalData['company'] ?? null,
                        'address' => $personalData['address'],
                        'address_suffix' => $personalData['address_suffix'] ?? null,
                        'postal_code' => $personalData['postal_code'],
                        'location' => $personalData['location'],
                        'country' => $personalData['country'],
                        'phone' => $personalData['phone'] ?? null,
                    ]
                );

                $confirmation_token = Str::uuid()->toString();
                $confirmationLink = route('participant.donate.confirm', ['projectId' => $projectId, 'participantId' => $participantId, 'token' => $confirmation_token]);

                // Handle translatable fields
                $name = $project->name;
                $description = $project->description;
                if (is_array($name)) {
                    $name = reset($name);
                }
                if (is_array($description)) {
                    $description = reset($description);
                }

                $startDateTime = $project->start;
                $date = $startDateTime ? $startDateTime->format('F d, Y') : null;
                $time = $startDateTime ? $startDateTime->format('H:i') : null;

                $donation = Donation::create([
                    'project_id' => $donationData['project_id'],
                    'participant_id' => $donationData['participant_id'],
                    'supporter_id' => $supporter->id, // Link to the supporter
                    'amount' => $donationData['amount'],
                    'currency' => $donationData['currency'],
                    'type' => 'flat-rate',
                    'billing_date' => now(),
                    'status' => 'pending',
                    'payment_method' => 'manual',
                    'supporter_email' => $email,
                    'confirmation_token' => $confirmation_token,
                ]);

                $request->session()->forget('donation_data');

                // Send confirmation email
                Mail::raw("Thank you for your donation! Please confirm your donation by clicking the following link: $confirmationLink", function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Please confirm your donation');
                });

                return Inertia::render('Projects/Participants/Donate', [
                    'project' => [
                        'id' => $project->id,
                        'name' => $name,
                        'description' => $description,
                        'date' => $date,
                        'time' => $time,
                        'location' => $project->location,
                        'image_url' => $project->image_landscape,
                    ],
                    'participant' => [
                        'id' => $participant->id,
                        'first_name' => $participant->first_name,
                        'last_name' => $participant->last_name,
                    ],
                    'step' => 'modal',
                    'confirmation_email' => $email,
                    'confirmation_link' => $confirmationLink,
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Resource not found: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Project or Participant not found.');
        } catch (\Exception $e) {
            Log::error('Failed to process donation: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to process donation. Please try again.');
        }
    }

    public function confirmDonation(Request $request, $projectId, $participantId, $token)
    {
        try {
            $donation = Donation::where('project_id', $projectId)
                ->where('participant_id', $participantId)
                ->where('confirmation_token', $token)
                ->where('status', 'pending')
                ->firstOrFail();

            $project = Project::findOrFail($projectId);

            // Handle translatable fields
            $name = $project->name;
            $description = $project->description;
            if (is_array($name)) {
                $name = reset($name);
            }
            if (is_array($description)) {
                $description = reset($description);
            }

            $startDateTime = $project->start;
            $date = $startDateTime ? $startDateTime->format('F d, Y') : null;
            $time = $startDateTime ? $startDateTime->format('H:i') : null;

            $donation->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Redirect to payment options page (to be implemented)
            return redirect()->route('participant.donate.payment', [
                'projectId' => $projectId,
                'participantId' => $participantId,
                'donationId' => $donation->id,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Donation not found: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', 'Invalid confirmation link.');
        } catch (\Exception $e) {
            Log::error('Failed to confirm donation: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->route('dashboard')->with('error', 'Failed to confirm donation.');
        }
    }

    public function showPaymentOptions(Request $request, $projectId, $participantId, $donationId)
    {
        try {
            $donation = \App\Models\Donation::where('id', $donationId)
                ->where('project_id', $projectId)
                ->where('participant_id', $participantId)
                ->firstOrFail();
            $project = Project::findOrFail($projectId);
            $participant = Participant::findOrFail($participantId);

            return Inertia::render('Projects/Participants/DonationPayment', [
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                ],
                'participant' => [
                    'id' => $participant->id,
                    'first_name' => $participant->first_name,
                    'last_name' => $participant->last_name,
                ],
                'donation' => [
                    'id' => $donation->id,
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load payment options: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', 'Failed to load payment options.');
        }
    }
}
