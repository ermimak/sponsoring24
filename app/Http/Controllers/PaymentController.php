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
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in Stripe webhook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature in Stripe webhook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Received Stripe webhook event', [
            'event_type' => $event->type,
            'event_id' => $event->id
        ]);

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                // Handle Stripe Checkout Session completion
                $session = $event->data->object;
                
                // Check if this is a donation payment
                if (isset($session['metadata']['donation_id'])) {
                    $donationId = $session['metadata']['donation_id'];
                    $paymentIntentId = $session['payment_intent'];
                    
                    Log::info('Processing donation payment from Checkout Session', [
                        'donation_id' => $donationId,
                        'payment_intent_id' => $paymentIntentId,
                        'session_id' => $session->id
                    ]);
                    
                    return $this->processDonationPayment($donationId, $session);
                }
                break;
                
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                
                // Check if this is a donation payment
                if (isset($paymentIntent->metadata->donation_id)) {
                    $donationId = $paymentIntent->metadata->donation_id;
                    
                    Log::info('Processing donation payment from Payment Intent', [
                        'donation_id' => $donationId,
                        'payment_intent_id' => $paymentIntent->id
                    ]);
                    
                    return $this->processDonationPayment($donationId, $paymentIntent);
                }
                break;
                
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                
                // Check if this is a donation payment
                if (isset($paymentIntent->metadata->donation_id)) {
                    $donationId = $paymentIntent->metadata->donation_id;
                    $donation = Donation::find($donationId);
                    
                    if ($donation) {
                        try {
                            // Use a transaction for ACID compliance
                            \DB::beginTransaction();
                            
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
                            
                            \DB::commit();
                            
                            Log::info('Donation payment failed', [
                                'donation_id' => $donation->id,
                                'payment_intent_id' => $paymentIntent->id,
                                'error' => $paymentIntent->last_payment_error ? $paymentIntent->last_payment_error->message : null
                            ]);
                        } catch (\Exception $e) {
                            \DB::rollBack();
                            
                            Log::error('Error processing failed donation payment', [
                                'donation_id' => $donationId,
                                'payment_intent_id' => $paymentIntent->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    } else {
                        Log::error('Donation not found for failed payment', [
                            'donation_id' => $donationId,
                            'payment_intent_id' => $paymentIntent->id
                        ]);
                    }
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }
    
    /**
     * Process a successful donation payment
     * 
     * @param int $donationId
     * @param object $paymentData Either a Stripe PaymentIntent or Checkout Session
     * @return \Illuminate\Http\Response
     */
    private function processDonationPayment($donationId, $paymentData)
    {
        $donation = Donation::find($donationId);
        
        if (!$donation) {
            Log::error('Donation not found for payment', [
                'donation_id' => $donationId,
                'payment_data' => $paymentData->id
            ]);
            return response()->json(['status' => 'error', 'message' => 'Donation not found'], 404);
        }
        
        // Check if this donation has already been processed
        if ($donation->status === 'completed' && $donation->payment_id) {
            Log::info('Donation already processed, skipping', [
                'donation_id' => $donation->id,
                'payment_id' => $donation->payment_id
            ]);
            return response()->json(['status' => 'success', 'message' => 'Donation already processed']);
        }
        
        try {
            // Use a transaction for ACID compliance
            \DB::beginTransaction();
            
            // Extract payment ID based on the type of payment data
            $paymentId = $paymentData instanceof \Stripe\PaymentIntent 
                ? $paymentData->id 
                : ($paymentData['payment_intent'] ?? $paymentData->id);
            
            // Extract payment method
            $paymentMethod = isset($paymentData->metadata) && isset($paymentData->metadata->payment_method)
                ? $paymentData->metadata->payment_method
                : (isset($paymentData['metadata']['payment_method']) 
                    ? $paymentData['metadata']['payment_method'] 
                    : 'card');
            
            // Update the donation record
            $donation->update([
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'payment_id' => $paymentId,
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
                    'payment_id' => $paymentId,
                    'payment_method' => $paymentMethod
                ]);
            }
            
            \DB::commit();
            
            Log::info('Donation payment processed successfully', [
                'donation_id' => $donation->id,
                'payment_id' => $paymentId,
                'amount' => $donation->amount,
                'currency' => $donation->currency
            ]);
            
            // Send notifications outside of the transaction to avoid blocking
            $this->sendDonationNotifications($donation, $project);
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \DB::rollBack();
            
            Log::error('Error processing donation payment', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Error processing payment'], 500);
        }
    }
    
    /**
     * Send notifications for a successful donation
     * 
     * @param Donation $donation
     * @param mixed $project
     * @return void
     */
    private function sendDonationNotifications($donation, $project)
    {
        try {
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
            
            Log::info('Donation notifications sent', [
                'donation_id' => $donation->id,
                'project_id' => $donation->project_id
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending donation notifications', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
