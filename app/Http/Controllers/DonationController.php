<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Project;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

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
                ->with(['participant', 'supporter']);

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
                'exception' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to load donations.');
        }
    }

    public function fetchDonations(Request $request, $projectId)
    {
        try {
            $query = Donation::where('project_id', $projectId)
                ->with(['participant', 'supporter']);

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

            return response()->json(['data' => $donations]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch donations: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch donations.'], 500);
        }
    }

    public function sendMassEmail(Request $request, $projectId)
    {
        try {
            $request->validate([
                'donation_ids' => 'required|array',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $donations = Donation::whereIn('id', $request->donation_ids)
                ->where('project_id', $projectId)
                ->get();

            foreach ($donations as $donation) {
                if ($donation->supporter_email) {
                    Log::info('Sending email to: ' . $donation->supporter_email, [
                        'subject' => $request->subject,
                        'message' => $request->message,
                    ]);
                    // Example with Laravel Mail (uncomment for production):
                    /*
                    Mail::raw($request->message, function ($mail) use ($donation, $request) {
                        $mail->to($donation->supporter_email)
                             ->subject($request->subject);
                    });
                    */
                }
            }

            return redirect()->back()->with('success', 'Mass email sent successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to send mass email: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to send mass email.');
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
                'exception' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to generate bulk invoice: ' . $e->getMessage());
        }
    }
}