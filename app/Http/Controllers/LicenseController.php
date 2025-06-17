<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\User;
use App\Notifications\LicenseNotification;
use App\Services\LicenseService;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    
    public function __construct(LicenseService $licenseService)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->licenseService = $licenseService;
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
        try {
            $user = Auth::user();
            $discountEligible = $user->discount_eligible && !$user->discount_used;
            $licensePrice = $discountEligible ? self::ANNUAL_LICENSE_PRICE - self::REFERRAL_DISCOUNT : self::ANNUAL_LICENSE_PRICE;
            
            // Create a PaymentIntent with the license amount and currency
            $paymentIntent = PaymentIntent::create([
                'amount' => $licensePrice * 100, // Convert to cents
                'currency' => 'chf',
                'metadata' => [
                    'user_id' => $user->id,
                    'license_type' => 'annual',
                    'discount_applied' => $discountEligible ? 'yes' : 'no',
                    'discount_amount' => $discountEligible ? self::REFERRAL_DISCOUNT : 0,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Log payment intent creation activity
            UserActivityService::logPayment('license_payment_intent_created', $user->id, [
                'amount' => $licensePrice,
                'currency' => 'CHF',
                'discount_applied' => $discountEligible,
                'discount_amount' => $discountEligible ? self::REFERRAL_DISCOUNT : 0,
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $licensePrice,
                'currency' => 'CHF',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating license payment intent', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json(['error' => 'Unable to create payment intent'], 500);
        }
    }

    /**
     * Handle successful license payment webhook
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            // Handle the event
            if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;
                $metadata = $paymentIntent->metadata;
                
                if (isset($metadata->license_type) && $metadata->license_type === 'annual') {
                    $userId = $metadata->user_id;
                    $user = User::find($userId);
                    
                    if ($user) {
                        // If discount was applied, mark it as used
                        if (isset($metadata->discount_applied) && $metadata->discount_applied === 'yes') {
                            $user->discount_used = true;
                            $user->save();
                            
                            // Log the discount usage
                            Log::info('License discount applied', [
                                'user_id' => $user->id,
                                'discount_amount' => $metadata->discount_amount,
                            ]);
                            
                            // Find the referrer and credit them with the bonus
                            $bonusCredit = \App\Models\BonusCredit::where('referred_user_id', $user->id)
                                ->where('type', 'referral')
                                ->where('credited', false)
                                ->first();
                                
                            if ($bonusCredit) {
                                $referrer = \App\Models\User::find($bonusCredit->user_id);
                                
                                if ($referrer) {
                                    // Credit the bonus
                                    $bonusCredit->credited = true;
                                    $bonusCredit->amount = 100.00; // CHF 100 bonus
                                    $bonusCredit->save();
                                    
                                    // Notify the referrer
                                    $referrer->notify(new \App\Notifications\ReferralBonusNotification($bonusCredit));
                                    
                                    // Log the bonus credit
                                    Log::info('Referral bonus credited', [
                                        'referrer_id' => $referrer->id,
                                        'referred_user_id' => $user->id,
                                        'bonus_amount' => $bonusCredit->amount,
                                    ]);
                                }
                            }
                        }
                        
                        // Create license record in database
                        $licenseType = isset($metadata->license_type) ? $metadata->license_type : 'annual';
                        $license = $this->licenseService->createLicense(
                            $user,
                            $paymentIntent->id,
                            $paymentIntent->amount / 100, // Convert from cents
                            strtoupper($paymentIntent->currency),
                            $licenseType,
                            isset($metadata->discount_applied) && $metadata->discount_applied === 'yes',
                            $metadata->discount_amount ?? 0,
                            [
                                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                                'customer_email' => $user->email,
                            ]
                        );
                        
                        Log::info('License created', [
                            'user_id' => $user->id,
                            'license_key' => $license->license_key,
                            'expires_at' => $license->expires_at,
                        ]);
                        
                        // Notify the user about successful license purchase
                        $user->notify(new LicenseNotification($user, 'purchase_success', [
                            'amount' => $paymentIntent->amount / 100, // Convert from cents
                            'currency' => strtoupper($paymentIntent->currency),
                            'discount_applied' => isset($metadata->discount_applied) && $metadata->discount_applied === 'yes',
                            'discount_amount' => $metadata->discount_amount ?? 0,
                            'license_key' => $license->license_key,
                            'license_type' => $license->type,
                            'expires_at' => $license->expires_at ? $license->expires_at->format('Y-m-d') : null,
                        ]));
                        
                        // Log the successful payment
                        UserActivityService::logPayment('license_payment_succeeded', $user->id, [
                            'amount' => $paymentIntent->amount / 100,
                            'currency' => strtoupper($paymentIntent->currency),
                            'payment_id' => $paymentIntent->id,
                            'discount_applied' => isset($metadata->discount_applied) && $metadata->discount_applied === 'yes',
                            'discount_amount' => $metadata->discount_amount ?? 0,
                        ]);
                    }
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
