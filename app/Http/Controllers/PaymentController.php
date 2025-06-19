<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric',
                'currency' => 'required|string|size:3',
                'donation_id' => 'required|exists:donations,id',
                'payment_method' => 'required|string|in:card,twint,invoice',
            ]);

            $donation = Donation::findOrFail($request->donation_id);
            // Set payment method types based on request
            $paymentMethodTypes = [];
            if ($request->payment_method === 'card') {
                $paymentMethodTypes = ['card'];
            } elseif ($request->payment_method === 'twint') {
                // TWINT requires specific configuration according to Stripe docs
                $paymentMethodTypes = ['twint'];
            }

            // Create a PaymentIntent with the order amount and currency
            $paymentIntentData = [
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => strtolower($request->currency),
                'metadata' => [
                    'donation_id' => $donation->id,
                    'project_id' => $donation->project_id,
                    'participant_id' => $donation->participant_id,
                    'payment_method' => $request->payment_method,
                ],
            ];
            
            // Add payment method types if specified, otherwise use automatic payment methods
            if (!empty($paymentMethodTypes)) {
                $paymentIntentData['payment_method_types'] = $paymentMethodTypes;
            } else {
                $paymentIntentData['automatic_payment_methods'] = [
                    'enabled' => true,
                ];
            }
            
            $paymentIntent = PaymentIntent::create($paymentIntentData);

            // Log payment intent creation activity
            if (Auth::check()) {
                UserActivityService::logPayment('payment_intent_created', Auth::id(), [
                    'donation_id' => $donation->id,
                    'project_id' => $donation->project_id,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'payment_intent_id' => $paymentIntent->id,
                    'payment_method' => $request->payment_method,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }
            
            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json(['error' => 'Stripe API error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Failed to create payment intent: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json(['error' => 'Failed to create payment intent'], 500);
        }
    }

    public function requestInvoice(Request $request)
    {
        try {
            $request->validate([
                'donation_id' => 'required|exists:donations,id',
                'billing_info' => 'required|array',
                'billing_info.name' => 'required|string|max:255',
                'billing_info.address' => 'required|string|max:255',
                'billing_info.email' => 'required|email|max:255',
            ]);

            $donation = Donation::findOrFail($request->donation_id);
            
            // Update donation with billing info and set status to 'invoice_requested'
            $donation->update([
                'status' => 'invoice_requested',
                'payment_method' => 'invoice',
                'billing_name' => $request->billing_info['name'],
                'billing_address' => $request->billing_info['address'],
                'billing_email' => $request->billing_info['email'],
            ]);

            // Load related project and participant
            $donation->load(['project', 'participant']);
            $project = $donation->project;
            
            // Log invoice request activity
            if (Auth::check()) {
                UserActivityService::logPayment('invoice_requested', Auth::id(), [
                    'donation_id' => $donation->id,
                    'project_id' => $donation->project_id,
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                ]);
            }
            
            // Send notification to project owner
            if ($project && $project->created_by) {
                $projectOwner = User::find($project->created_by);
                if ($projectOwner) {
                    $projectOwner->notify(new PaymentReceivedNotification(
                        $donation, 
                        $donation->amount, 
                        $project->name,
                        'invoice_requested'
                    ));
                }
            }
            
            // Send notification to admin users
            $admins = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new PaymentReceivedNotification(
                    $donation, 
                    $donation->amount, 
                    $project ? $project->name : null,
                    'invoice_requested'
                ));
            }

            return response()->json(['message' => 'Invoice request processed successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to process invoice request: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json(['error' => 'Failed to process invoice request'], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $donation = Donation::find($paymentIntent->metadata->donation_id);
                
                if ($donation) {
                    $donation->update([
                        'status' => 'completed',
                        'payment_method' => $paymentIntent->metadata->payment_method ?? 'card',
                        'payment_id' => $paymentIntent->id,
                        'paid_at' => now(),
                    ]);
                    
                    // Load related project and participant
                    $donation->load(['project', 'participant']);
                    $project = $donation->project;
                    
                    // Log successful payment activity
                    if ($donation->user_id) {
                        UserActivityService::logPayment('payment_succeeded', $donation->user_id, [
                            'donation_id' => $donation->id,
                            'project_id' => $donation->project_id,
                            'amount' => $donation->amount,
                            'currency' => $donation->currency,
                            'payment_intent_id' => $paymentIntent->id,
                            'payment_method' => $paymentIntent->metadata->payment_method ?? 'card'
                        ]);
                    }
                    
                    // Send notification to project owner
                    if ($project && $project->created_by) {
                        $projectOwner = User::find($project->created_by);
                        if ($projectOwner) {
                            $projectOwner->notify(new PaymentReceivedNotification(
                                $donation, 
                                $donation->amount, 
                                $project->name
                            ));
                        }
                    }
                    
                    // Send notification to admin users
                    $admins = User::whereHas('roles', function($query) {
                        $query->whereIn('name', ['admin', 'super-admin']);
                    })->get();
                    
                    foreach ($admins as $admin) {
                        $admin->notify(new PaymentReceivedNotification(
                            $donation, 
                            $donation->amount, 
                            $project ? $project->name : null
                        ));
                    }
                }

                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $donation = Donation::find($paymentIntent->metadata->donation_id);
                
                if ($donation) {
                    $donation->update([
                        'status' => 'failed',
                        'payment_method' => $paymentIntent->metadata->payment_method ?? 'card',
                        'payment_id' => $paymentIntent->id,
                    ]);
                    
                    // Log failed payment activity
                    if ($donation->user_id) {
                        UserActivityService::logPayment('payment_failed', $donation->user_id, [
                            'donation_id' => $donation->id,
                            'project_id' => $donation->project_id,
                            'amount' => $donation->amount,
                            'currency' => $donation->currency,
                            'payment_intent_id' => $paymentIntent->id,
                            'payment_method' => $paymentIntent->metadata->payment_method ?? 'card',
                            'error' => $paymentIntent->last_payment_error ? $paymentIntent->last_payment_error->message : null
                        ]);
                    }
                }

                break;
        }

        return response()->json(['status' => 'success']);
    }
}
