<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Donation;
use App\Models\Project;
use App\Models\Participant;

class AuditLogService
{
    /**
     * Log financial transaction events
     */
    public static function logFinancialTransaction(string $event, array $data, ?string $userId = null): void
    {
        $logData = [
            'event_type' => 'financial_transaction',
            'event' => $event,
            'user_id' => $userId ?? (Auth::check() ? Auth::id() : null),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'data' => $data,
            'session_id' => session()->getId(),
        ];

        // Log to Laravel log with structured data
        Log::channel('audit')->info('Financial Transaction', $logData);

        // Also log to security channel for critical events
        if (in_array($event, ['donation_created', 'payment_failed', 'refund_processed', 'suspicious_activity'])) {
            Log::channel('security')->warning('Critical Financial Event', $logData);
        }
    }

    /**
     * Log donation lifecycle events
     */
    public static function logDonationEvent(string $event, Donation $donation, array $additionalData = []): void
    {
        $data = array_merge([
            'donation_id' => $donation->id,
            'amount' => $donation->amount,
            'currency' => $donation->currency,
            'participant_id' => $donation->participant_id,
            'project_id' => $donation->project_id,
            'payment_status' => $donation->payment_status ?? 'pending',
            'donor_email' => $donation->donor_email,
            'payment_method' => $donation->payment_method ?? 'unknown',
        ], $additionalData);

        self::logFinancialTransaction($event, $data);
    }

    /**
     * Log suspicious activity
     */
    public static function logSuspiciousActivity(string $description, array $data = []): void
    {
        $logData = array_merge([
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
        ], $data);

        self::logFinancialTransaction('suspicious_activity', $logData);
    }

    /**
     * Log access control events
     */
    public static function logAccessControl(string $event, array $data = []): void
    {
        $logData = array_merge([
            'user_id' => Auth::check() ? Auth::id() : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString(),
        ], $data);

        Log::channel('security')->info('Access Control Event: ' . $event, $logData);
    }

    /**
     * Log payment processing events
     */
    public static function logPaymentEvent(string $event, array $data = []): void
    {
        $logData = array_merge([
            'payment_processor' => 'stripe',
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
        ], $data);

        self::logFinancialTransaction($event, $logData);
    }

    /**
     * Detect and log potential fraud patterns
     */
    public static function detectFraudPatterns(Donation $donation): void
    {
        $suspiciousPatterns = [];

        // Check for unusually high amounts
        if ($donation->amount > 10000) {
            $suspiciousPatterns[] = 'high_amount';
        }

        // Check for rapid successive donations from same IP
        $recentDonations = Donation::where('created_at', '>', now()->subMinutes(5))
            ->whereHas('supporter', function($query) {
                $query->where('ip_address', request()->ip());
            })
            ->count();

        if ($recentDonations > 3) {
            $suspiciousPatterns[] = 'rapid_donations';
        }

        // Check for suspicious email patterns
        if (preg_match('/\+.*@|temp|disposable|fake/i', $donation->donor_email)) {
            $suspiciousPatterns[] = 'suspicious_email';
        }

        if (!empty($suspiciousPatterns)) {
            self::logSuspiciousActivity('Potential fraud patterns detected', [
                'donation_id' => $donation->id,
                'patterns' => $suspiciousPatterns,
                'amount' => $donation->amount,
                'donor_email' => $donation->donor_email,
            ]);
        }
    }

    /**
     * Log data export events for compliance
     */
    public static function logDataExport(string $exportType, array $data = []): void
    {
        $logData = array_merge([
            'export_type' => $exportType,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString(),
        ], $data);

        Log::channel('audit')->info('Data Export', $logData);
    }
}
