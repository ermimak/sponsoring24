<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\User;
use App\Notifications\LicenseNotification;
use App\Services\LicenseService;
use App\Services\ReferralService;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class LicenseController extends Controller
{
    /**
     * Standard annual license price in CHF
     */
    const ANNUAL_LICENSE_PRICE = 500.00;
    
    /**
     * Discount amount for referred users in CHF
     */
    const REFERRAL_DISCOUNT = 50.00;

    protected $licenseService;
    protected $referralService;
    
    public function __construct(LicenseService $licenseService, ReferralService $referralService)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->licenseService = $licenseService;
        $this->referralService = $referralService;
    }

    /**
     * Display the license dashboard page.
     *
     * @return \Inertia\Response
     */
    public function dashboard()
    {
        $user = Auth::user();
        $license = $this->licenseService->getActiveLicense($user);
        
        if ($license) {
            // Add days remaining for the view
            $license->days_remaining = $license->daysRemaining();
        }
        
        return Inertia::render('Dashboard/License', [
            'license' => $license
        ]);
    }

    /**
     * Display detailed information about a specific license.
     *
     * @param string $licenseId
     * @return \Inertia\Response
     */
    public function showDetail($licenseId = null)
    {
        $user = Auth::user();
        
        // If no license ID is provided, get the active license
        if (!$licenseId) {
            $license = $this->licenseService->getActiveLicense($user);
        } else {
            // Otherwise, get the specific license if it belongs to the user
            $license = License::where('id', $licenseId)
                ->where('user_id', $user->id)
                ->first();
        }
        
        if ($license) {
            // Add days remaining for the view
            $license->days_remaining = $license->daysRemaining();
            
            // Log the license view
            Log::info('User viewed license details', [
                'user_id' => $user->id,
                'license_id' => $license->id,
                'license_key' => $license->license_key,
            ]);
        }
        
        return Inertia::render('License/Detail', ['license' => $license]);
    }

    /**
     * Display the license purchase page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if the user has an active license
        // This would typically be a relationship or a separate query
        // For now, we'll just check if the user has a license_key and if it's not expired
        $licenseData = null;
        
        // TODO: Replace with actual license data from database when license model is implemented
        // For now, we'll just use a placeholder
        if ($user->license_key) {
            $licenseData = [
                'license_key' => $user->license_key,
                'created_at' => now()->subYear()->format('Y-m-d H:i:s'),
                'expires_at' => now()->addMonths(3)->format('Y-m-d H:i:s'), // Example expiry date
                'is_active' => true,
            ];
        }
        
        return Inertia::render('License/Index', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'referral_code' => $user->referral_code,
                'discount_eligible' => $user->discount_eligible,
                'discount_used' => $user->discount_used,
                'has_active_license' => !empty($licenseData),
                'license_key' => $user->license_key ?? null,
            ],
            'stripePublishableKey' => config('services.stripe.key'),
            'licenseData' => $licenseData,
        ]);
    }

    /**
     * Create a payment intent for license purchase
     */
    public function createPaymentIntent(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        // Check if user already has an active license
        $hasActiveLicense = License::where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
            
        if ($hasActiveLicense) {
            return response()->json(['error' => 'User already has an active license'], 400);
        }
        
        try {
            // Check if user is eligible for a discount
            $discountEligible = $request->input('apply_discount') && $user->discount_eligible && !$user->discount_used;
            
            // Calculate license price with discount if applicable
            $licensePrice = $discountEligible ? self::ANNUAL_LICENSE_PRICE - self::REFERRAL_DISCOUNT : self::ANNUAL_LICENSE_PRICE;
            
            // Create a Stripe Checkout Session instead of a PaymentIntent
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'chf',
                        'product_data' => [
                            'name' => 'Annual License',
                            'description' => 'Fundoo Annual License Subscription',
                        ],
                        'unit_amount' => $licensePrice * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'user_id' => $user->id,
                    'license_type' => 'annual',
                    'discount_applied' => $discountEligible ? 'yes' : 'no',
                    'discount_amount' => $discountEligible ? self::REFERRAL_DISCOUNT : 0,
                ],
                'mode' => 'payment',
                'success_url' => route('license.success'),
                'cancel_url' => route('license.purchase'),
                'customer_email' => $user->email,
            ]);

            // Log checkout session creation activity
            UserActivityService::logPayment('license_checkout_session_created', $user->id, [
                'session_id' => $session->id,
                'amount' => $licensePrice,
                'discount_applied' => $discountEligible,
            ]);
            
            return response()->json([
                'sessionId' => $session->id,
                'amount' => $licensePrice,
                'discountApplied' => $discountEligible,
            ]);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Handle network connectivity issues
            Log::error('Stripe API connection error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            
            // For local development, create a license directly if we can't connect to Stripe
            if (app()->environment('local', 'development', 'testing')) {
                try {
                    Log::warning('Creating license directly in dev mode due to Stripe connection failure', [
                        'user_id' => $user->id,
                        'environment' => app()->environment(),
                    ]);
                    
                    // Begin transaction
                    \DB::beginTransaction();
                    
                    // Create a mock payment ID for local development
                    $mockPaymentId = 'dev_' . Str::random(24);
                    
                    // Create license with standard values
                    $license = $this->licenseService->createLicense(
                        $user,
                        $mockPaymentId,
                        500.00, // Standard license price
                        'CHF',
                        'annual',
                        $user->discount_eligible && !$user->discount_used,
                        $user->discount_eligible && !$user->discount_used ? self::REFERRAL_DISCOUNT : 0,
                        [
                            'payment_method' => 'card',
                            'customer_email' => $user->email,
                            'dev_mode' => true,
                            'created_at' => now()->toDateTimeString(),
                        ]
                    );
                    
                    // If discount was applied, mark it as used
                    if ($user->discount_eligible && !$user->discount_used) {
                        $user->discount_used = true;
                        $user->save();
                        
                        // Credit any referral bonus
                        $this->referralService->creditReferralBonus(
                            $user,
                            500.00,
                            'CHF',
                            $mockPaymentId
                        );
                    }
                    
                    // Commit transaction
                    \DB::commit();
                    
                    Log::info('License created directly in dev mode', [
                        'user_id' => $user->id,
                        'license_id' => $license->id,
                        'license_key' => $license->license_key,
                        'dev_mode' => true,
                    ]);
                    
                    // Return success with redirect URL to success page
                    return response()->json([
                        'success' => true,
                        'message' => 'License created successfully in development mode',
                        'redirect' => route('license.success'),
                        'dev_mode' => true,
                    ]);
                    
                } catch (\Exception $devEx) {
                    // Roll back transaction if anything fails
                    \DB::rollBack();
                    
                    Log::error('Failed to create license in dev mode', [
                        'error' => $devEx->getMessage(),
                        'trace' => $devEx->getTraceAsString(),
                        'user_id' => $user->id,
                    ]);
                    
                    // Return the original error
                    return response()->json([
                        'error' => 'Could not connect to payment service and dev fallback failed. Please check your internet connection and try again.',
                        'details' => 'Network connectivity issue with payment provider.',
                        'dev_mode' => app()->environment('local', 'development', 'testing'),
                    ], 503);
                }
            } else {
                // In production, just return the error
                return response()->json([
                    'error' => 'Could not connect to payment service. Please check your internet connection and try again.',
                    'details' => 'Network connectivity issue with payment provider.',
                    'dev_mode' => false,
                ], 503);
            }
        } catch (\Exception $e) {
            Log::error('Error creating license checkout session', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            
            return response()->json(['error' => 'Failed to create checkout session: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display license purchase success page
     */
    public function success(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get the user's most recent license
        $license = License::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
            
        return Inertia::render('License/Success', [
            'license' => $license,
            'user' => $user,
        ]);
    }
    
    /**
     * Handle Stripe webhook for license payments
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            // In development environment, we might want to bypass signature verification
            // since we might be testing with forwarded webhooks or direct POST requests
            if (app()->environment('local', 'development', 'testing')) {
                Log::info('Development environment detected, attempting to parse webhook directly');
                try {
                    $event = json_decode($payload, true);
                    $event = \Stripe\Event::constructFrom($event);
                } catch (\Exception $e) {
                    Log::warning('Failed to parse webhook directly, falling back to signature verification', [
                        'error' => $e->getMessage()
                    ]);
                    // Fall back to normal signature verification
                    $event = \Stripe\Webhook::constructEvent(
                        $payload, $sig_header, $endpoint_secret
                    );
                }
            } else {
                // Production environment - always verify signature
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sig_header, $endpoint_secret
                );
            }

            // Log the event type for debugging
            Log::info('Stripe webhook received', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            // Handle the event based on type
            if ($event->type === 'checkout.session.completed') {
                // Handle Checkout Session completion
                $session = $event->data->object;
                $metadata = $session->metadata->toArray();
                
                Log::info('Checkout session completed', [
                    'session_id' => $session->id,
                    'metadata' => $metadata,
                ]);
                
                if (isset($metadata['license_type']) && $metadata['license_type'] === 'annual') {
                    $userId = $metadata['user_id'];
                    $user = User::find($userId);
                    
                    if ($user) {
                        // Use a database transaction for ACID compliance
                        \DB::beginTransaction();
                        
                        try {
                            // Get payment details from the session
                            $amount = $session->amount_total / 100; // Convert from cents
                            $currency = strtoupper($session->currency);
                            $paymentId = $session->payment_intent;
                            $discountApplied = isset($metadata['discount_applied']) && $metadata['discount_applied'] === 'yes';
                            $discountAmount = $metadata['discount_amount'] ?? 0;
                            
                            // If discount was applied, mark it as used
                            if ($discountApplied) {
                                $user->discount_used = true;
                                $user->save();
                                
                                // Log the discount usage
                                Log::info('License discount applied', [
                                    'user_id' => $user->id,
                                    'discount_amount' => $discountAmount,
                                ]);
                                
                                // Use the ReferralService to credit any referral bonus in an ACID-compliant way
                                $bonusResult = $this->referralService->creditReferralBonus(
                                    $user, 
                                    $amount, 
                                    $currency, 
                                    $paymentId
                                );
                                
                                if ($bonusResult) {
                                    Log::info('Referral bonus credited via ReferralService', [
                                        'bonus_credit_id' => $bonusResult['bonus_credit_id'],
                                        'referrer_id' => $bonusResult['referrer_id'],
                                        'amount' => $bonusResult['amount'],
                                        'currency' => $bonusResult['currency']
                                    ]);
                                }
                            }
                            
                            // Create license record in database
                            $licenseType = $metadata['license_type'] ?? 'annual';
                            $license = $this->licenseService->createLicense(
                                $user,
                                $paymentId,
                                $amount,
                                $currency,
                                $licenseType,
                                $discountApplied,
                                $discountAmount,
                                [
                                    'payment_method' => $session->payment_method_types[0] ?? 'card',
                                    'customer_email' => $user->email,
                                    'session_id' => $session->id,
                                ]
                            );
                            
                            // Commit the transaction
                            \DB::commit();
                            
                            Log::info('License created successfully', [
                                'user_id' => $user->id,
                                'license_key' => $license->license_key,
                                'expires_at' => $license->expires_at,
                                'transaction' => 'committed',
                            ]);
                            
                            // After successful transaction, send notifications
                            // These are outside the transaction as they're not critical for data integrity
                            try {
                                // Notify the user about successful license purchase
                                $user->notify(new LicenseNotification($user, 'purchase_success', [
                                    'amount' => $amount,
                                    'currency' => $currency,
                                    'discount_applied' => $discountApplied,
                                    'discount_amount' => $discountAmount,
                                    'license_key' => $license->license_key,
                                    'license_type' => $license->type,
                                    'expires_at' => $license->expires_at ? $license->expires_at->format('Y-m-d') : null,
                                ]));
                                
                                // Notify referrer if applicable
                                if ($discountApplied && isset($bonusCredit) && isset($referrer)) {
                                    $referrer->notify(new \App\Notifications\ReferralBonusNotification($bonusCredit));
                                }
                                
                                // Log the successful payment
                                UserActivityService::logPayment('license_payment_succeeded', $user->id, [
                                    'amount' => $amount,
                                    'currency' => $currency,
                                    'payment_id' => $paymentId,
                                    'discount_applied' => $discountApplied,
                                    'discount_amount' => $discountAmount,
                                ]);
                            } catch (\Exception $notificationException) {
                                // Log notification errors but don't fail the webhook
                                Log::error('Error sending notifications after license creation', [
                                    'error' => $notificationException->getMessage(),
                                    'user_id' => $user->id,
                                ]);
                            }
                        } catch (\Exception $e) {
                            // If anything goes wrong, roll back the transaction
                            \DB::rollBack();
                            
                            Log::error('License creation failed, transaction rolled back', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'user_id' => $user->id,
                                'session_id' => $session->id,
                            ]);
                            
                            throw $e; // Re-throw to be caught by the outer try-catch
                        }
                    } else {
                        Log::error('User not found for license creation', [
                            'user_id' => $userId,
                            'session_id' => $session->id,
                        ]);
                    }
                }
            } 
            // Also handle payment_intent.succeeded for backward compatibility
            else if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;
                $metadata = $paymentIntent->metadata->toArray();
                
                if (isset($metadata['license_type']) && $metadata['license_type'] === 'annual') {
                    $userId = $metadata['user_id'];
                    $user = User::find($userId);
                    
                    if ($user) {
                        // Use a database transaction for ACID compliance
                        \DB::beginTransaction();
                        
                        try {
                            $discountApplied = isset($metadata['discount_applied']) && $metadata['discount_applied'] === 'yes';
                            $discountAmount = $metadata['discount_amount'] ?? 0;
                            
                            // If discount was applied, mark it as used
                            if ($discountApplied) {
                                $user->discount_used = true;
                                $user->save();
                            }
                            
                            // Create license record in database
                            $licenseType = $metadata['license_type'] ?? 'annual';
                            $license = $this->licenseService->createLicense(
                                $user,
                                $paymentIntent->id,
                                $paymentIntent->amount / 100,
                                strtoupper($paymentIntent->currency),
                                $licenseType,
                                $discountApplied,
                                $discountAmount,
                                [
                                    'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                                    'customer_email' => $user->email,
                                ]
                            );
                            
                            // Commit the transaction
                            \DB::commit();
                            
                            Log::info('License created from payment intent', [
                                'user_id' => $user->id,
                                'license_key' => $license->license_key,
                                'transaction' => 'committed',
                            ]);
                        } catch (\Exception $e) {
                            // If anything goes wrong, roll back the transaction
                            \DB::rollBack();
                            
                            Log::error('License creation from payment intent failed, transaction rolled back', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'user_id' => $user->id,
                                'payment_intent_id' => $paymentIntent->id,
                            ]);
                            
                            throw $e; // Re-throw to be caught by the outer try-catch
                        }
                    } else {
                        Log::error('User not found for license creation from payment intent', [
                            'user_id' => $userId,
                            'payment_intent_id' => $paymentIntent->id,
                        ]);
                    }
                }
            }
            
            return response()->json(['status' => 'success']);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Handle signature verification errors
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            Log::error('Stripe API error in webhook', [
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Stripe API error: ' . $e->getMessage()], 400);
        } catch (\Exception $e) {
            // Handle all other exceptions
            Log::error('Stripe webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload_excerpt' => substr($payload, 0, 500) . '...',
            ]);
            
            // Save the webhook payload for debugging in development environments
            if (app()->environment('local', 'development', 'testing')) {
                $filename = 'failed_webhook_' . date('Y-m-d_H-i-s') . '.json';
                Storage::disk('local')->put('webhooks/' . $filename, $payload);
                Log::info('Failed webhook payload saved', ['filename' => $filename]);
            }
            
            return response()->json(['error' => 'Webhook processing error: ' . $e->getMessage()], 500);
        }
    }
}
