<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\EmailTemplate;
use App\Models\Project;
use App\Services\EmailService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class DonationController extends Controller
{
    public function index(Request $request, $projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            // Handle translatable fields
            $name = $project->name;
            if (is_array($name)) {
                $name = reset($name); // Use the first translation
            }

            // Fetch donations with filters
            $query = Donation::where('project_id', $projectId)
                ->with(['participant']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            if ($request->filled('date_from')) {
                $query->whereDate('billing_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('billing_date', '<=', $request->input('date_to'));
            }
            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->input('amount_min'));
            }
            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->input('amount_max'));
            }

            $donations = $query->get()->map(function ($donation) {
                return [
                    'id' => $donation->id,
                    'donor_name' => $donation->supporter_email ?? 'Anonymous',
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                    'date' => $donation->billing_date->format('Y-m-d'),
                    'status' => $donation->status,
                    'participant_name' => $donation->participant ? $donation->participant->first_name . ' ' . $donation->participant->last_name : 'N/A',
                ];
            });

            return Inertia::render('Projects/DonationsTab', [
                'project' => [
                    'id' => $project->id,
                    'name' => $name,
                ],
                'donations' => $donations,
                'filters' => $request->only(['status', 'date_from', 'date_to', 'amount_min', 'amount_max']),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Project not found: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Project not found.');
        } catch (\Exception $e) {
            Log::error('Failed to load donations: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to load donations.');
        }
    }

    public function fetchDonations(Request $request, $projectId)
    {
        try {
            $query = Donation::where('project_id', $projectId)
                ->with(['participant']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            if ($request->filled('date_from')) {
                $query->whereDate('billing_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('billing_date', '<=', $request->input('date_to'));
            }
            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->input('amount_min'));
            }
            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->input('amount_max'));
            }

            $donations = $query->get()->map(function ($donation) {
                $participantName = 'N/A';
                if ($donation->participant) {
                    $participantName = ($donation->participant->first_name ?? '') . ' ' . ($donation->participant->last_name ?? '');
                }

                $donorName = 'Anonymous';
                if ($donation->supporter) {
                    $donorName = $donation->supporter->email ?? 'Anonymous';
                } elseif ($donation->supporter_email) {
                    $donorName = $donation->supporter_email;
                }

                return [
                    'id' => $donation->id,
                    'donor_name' => $donorName,
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                    'date' => $donation->billing_date ? $donation->billing_date->format('Y-m-d') : null,
                    'status' => $donation->status,
                    'participant_name' => $participantName,
                    'participant_id' => $donation->participant_id,
                ];
            });

            return response()->json(['data' => $donations]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch donations: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                 'request_params' => $request->all(),
            ]);

            return response()->json(['error' => 'Failed to fetch donations.', 'details' => $e->getMessage()], 500);
        }
    }

    public function sendMassEmail(Request $request, $projectId)
    {
        try {
            $request->validate([
                'donation_ids' => 'required|array',
                'template_id' => 'required|exists:email_templates,id',
                'subject' => 'required|string|max:255',
                'body' => 'required|string',
            ]);

            $project = Project::findOrFail($projectId);
            $template = EmailTemplate::findOrFail($request->template_id);
            $emailService = new EmailService();
            
            $donations = Donation::whereIn('id', $request->donation_ids)
                ->where('project_id', $projectId)
                ->get();

            $results = [
                'success' => 0,
                'failed' => 0,
                'skipped' => 0,
            ];

            foreach ($donations as $donation) {
                if (!$donation->donor_email) {
                    $results['skipped']++;
                    continue;
                }
                
                $success = $emailService->sendToDonation(
                    $donation,
                    $template,
                    $request->subject,
                    $request->body
                );
                
                if ($success) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                }
            }

            return response()->json([
                'message' => 'Mass email sent successfully',
                'results' => $results
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to send mass email: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to send mass email: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Send an email to a single donation
     *
     * @param Request $request
     * @param int $donationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(Request $request, $donationId)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'subject' => 'required|string',
            'body' => 'required|string',
            'project_id' => 'required|exists:projects,id',
        ]);

        try {
            $donation = Donation::findOrFail($donationId);
            $template = EmailTemplate::findOrFail($request->template_id);
            $project = Project::findOrFail($request->project_id);
            
            $emailService = new EmailService();
            $success = $emailService->sendToDonation(
                $donation,
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
            Log::error('Failed to send email to donation ' . $donationId . ': ' . $e->getMessage());

            return response()->json(['error' => 'Failed to send email'], 500);
        }
    }

    public function bulkInvoice(Request $request, $projectId)
    {
        try {
            $request->validate([
                'donation_ids' => 'required|array',
            ]);

            $project = Project::findOrFail($projectId);
            $projectName = is_array($project->name) ? reset($project->name) : $project->name;

            $donations = Donation::whereIn('id', $request->donation_ids)
                ->where('project_id', $projectId)
                ->with(['participant'])
                ->get();

            if ($donations->isEmpty()) {
                return redirect()->back()->with('error', 'No valid donations selected for invoicing.');
            }

            // Prepare data for the PDF
            $invoiceData = $donations->map(function ($donation) {
                return [
                    'donor' => $donation->supporter_email ?? 'Anonymous',
                    'participant' => $donation->participant ? $donation->participant->first_name . ' ' . $donation->participant->last_name : 'N/A',
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                    'date' => $donation->billing_date->format('Y-m-d'),
                    'status' => $donation->status,
                ];
            })->toArray();

            // Load the PDF view
            $pdf = Pdf::loadView('pdf.bulk_invoice', [
                'project_name' => $projectName,
                'donations' => $invoiceData,
                'invoice_date' => now()->format('Y-m-d'),
            ]);

            // Set paper size (optional)
            $pdf->setPaper('A4', 'portrait');

            // Stream the PDF as a downloadable file
            return $pdf->download('bulk_invoice_' . $projectId . '_' . now()->format('Ymd') . '.pdf');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to generate bulk invoice: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to generate bulk invoice: ' . $e->getMessage());
        }
    }

    public function showPreview(Donation $donation)
    {
        try {
            $donation->load(['supporter', 'project', 'participant']); // Eager load necessary relationships

            $project = $donation->project;

            if (! $project) {
                abort(404, 'Project not found for this donation.');
            }

            $projectName = is_array($project->name) ? reset($project->name) : $project->name;

            $supporterData = [
                'name' => $donation->supporter ? $donation->supporter->name : null,
                'address' => $donation->supporter ? $donation->supporter->address : null,
                'city' => $donation->supporter ? $donation->supporter->city : null,
                'postal_code' => $donation->supporter ? $donation->supporter->postal_code : null,
                'country' => $donation->supporter ? $donation->supporter->country : null,
                'email' => $donation->supporter ? $donation->supporter->email : ($donation->supporter_email ?? null),
            ];

            // Return Inertia render for web view
            return Inertia::render('Projects/Donations/DonationPreview', [
                'donation' => [
                    'id' => $donation->id,
                    'supporter_email' => $donation->supporter_email ?? 'Anonymous',
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                    'billing_date' => $donation->billing_date ? $donation->billing_date->format('Y-m-d') : null,
                    'status' => $donation->status,
                    'participant_name' => $donation->participant ? ($donation->participant->first_name ?? '') . ' ' . ($donation->participant->last_name ?? '') : 'N/A',
                ],
                'project' => [
                    'id' => $project->id,
                    'name' => $projectName,
                    'description' => is_array($project->description) ? reset($project->description) : $project->description,
                    'image_landscape' => $project->image_landscape,
                ],
                'supporter' => $supporterData,
                // Include dummy data for rounding up and payment button for visual match
                'showRoundingUp' => true,
                'roundingUpAmount' => 0,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Donation not found: ' . $e->getMessage());
            abort(404, 'Donation not found.');
        } catch (\Exception $e) {
            Log::error('Failed to generate donation preview: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);
            abort(500, 'Failed to generate donation preview.');
        }
    }
}
