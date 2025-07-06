<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

// This script simulates a Stripe webhook event to test license creation

// Configuration
$webhookUrl = 'http://localhost:8000/webhook/license/stripe';
$userId = 1; // Replace with a valid user ID from your database
$sessionId = 'cs_test_' . uniqid();
$paymentIntentId = 'pi_test_' . uniqid();

// Create a mock checkout.session.completed event
$event = [
    'id' => 'evt_test_' . uniqid(),
    'object' => 'event',
    'api_version' => '2020-08-27',
    'created' => time(),
    'data' => [
        'object' => [
            'id' => $sessionId,
            'object' => 'checkout.session',
            'amount_total' => 50000, // 500.00 CHF in cents
            'currency' => 'chf',
            'customer_email' => 'test@example.com',
            'payment_intent' => $paymentIntentId,
            'payment_status' => 'paid',
            'metadata' => [
                'user_id' => $userId,
                'license_type' => 'annual',
                'discount_applied' => 'no',
                'discount_amount' => 0,
            ],
            'payment_method_types' => ['card'],
        ],
    ],
    'type' => 'checkout.session.completed',
];

echo "Sending test webhook event to: $webhookUrl\n";
echo "User ID: $userId\n";
echo "Session ID: $sessionId\n";
echo "Payment Intent ID: $paymentIntentId\n\n";

// Send the webhook event
$client = new Client();
try {
    $response = $client->post($webhookUrl, [
        'json' => $event,
        'headers' => [
            'Content-Type' => 'application/json',
            'Stripe-Signature' => 'test_signature', // This is a mock signature
        ],
    ]);

    echo "Response status code: " . $response->getStatusCode() . "\n";
    echo "Response body: " . $response->getBody() . "\n";
    
    // Check if license was created
    echo "\nChecking if license was created...\n";
    echo "Run the following SQL query in your database:\n";
    echo "SELECT * FROM licenses WHERE payment_id = '$paymentIntentId';\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
