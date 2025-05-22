<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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
                'donation_id' => 'required|exists:donations,id'
            ]);

            $donation = Donation::findOrFail($request->donation_id);

            // Create a PaymentIntent with the order amount and currency
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => strtolower($request->currency),
                'metadata' => [
                    'donation_id' => $donation->id,
                    'project_id' => $donation->project_id,
                    'participant_id' => $donation->participant_id,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create payment intent: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'Failed to create payment intent'], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
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
                        'payment_method' => 'card',
                        'payment_id' => $paymentIntent->id,
                        'paid_at' => now(),
                    ]);
                }
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $donation = Donation::find($paymentIntent->metadata->donation_id);
                
                if ($donation) {
                    $donation->update([
                        'status' => 'failed',
                        'payment_method' => 'card',
                        'payment_id' => $paymentIntent->id,
                    ]);
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }
} 