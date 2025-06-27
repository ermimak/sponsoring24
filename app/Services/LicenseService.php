<?php

namespace App\Services;

use App\Models\License;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LicenseService
{
    /**
     * Generate a unique license key.
     *
     * @return string
     */
    public function generateLicenseKey()
    {
        $prefix = 'FUNDOO';
        $randomPart = strtoupper(Str::random(16));
        $licenseKey = $prefix . '-' . $randomPart;
        
        // Ensure the license key is unique
        while (License::where('license_key', $licenseKey)->exists()) {
            $randomPart = strtoupper(Str::random(16));
            $licenseKey = $prefix . '-' . $randomPart;
        }
        
        return $licenseKey;
    }
    
    /**
     * Create a new license for a user.
     *
     * @param User $user
     * @param string $paymentId
     * @param float $amount
     * @param string $currency
     * @param string $type
     * @param bool $discountApplied
     * @param float $discountAmount
     * @param array $metadata
     * @return License
     * @throws \Exception If license creation fails
     */
    public function createLicense(
        User $user,
        string $paymentId,
        float $amount,
        string $currency = 'CHF',
        string $type = 'annual',
        bool $discountApplied = false,
        float $discountAmount = 0,
        array $metadata = []
    ) {
        // Check if a license with this payment ID already exists
        // This prevents duplicate licenses from being created if webhook is called multiple times
        $existingLicense = License::where('payment_id', $paymentId)->first();
        if ($existingLicense) {
            \Log::warning('Attempted to create duplicate license', [
                'payment_id' => $paymentId,
                'user_id' => $user->id,
                'existing_license_id' => $existingLicense->id
            ]);
            return $existingLicense;
        }
        
        $expiresAt = null;
        
        // Set expiration date based on license type
        if ($type === 'annual') {
            $expiresAt = Carbon::now()->addYear();
        } elseif ($type === 'monthly') {
            $expiresAt = Carbon::now()->addMonth();
        }
        
        // Generate a unique license key
        $licenseKey = $this->generateLicenseKey();
        
        // Create the license record with explicit transaction handling
        try {
            // Create the license record
            $license = License::create([
                'user_id' => $user->id,
                'license_key' => $licenseKey,
                'status' => 'active',
                'type' => $type,
                'issued_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'payment_id' => $paymentId,
                'amount' => $amount,
                'currency' => $currency,
                'discount_applied' => $discountApplied,
                'discount_amount' => $discountAmount,
                'metadata' => $metadata
            ]);
            
            // Ensure the license was created
            if (!$license || !$license->exists) {
                throw new \Exception('Failed to create license record');
            }
            
            // Log successful license creation
            \Log::info('License record created', [
                'license_id' => $license->id,
                'license_key' => $license->license_key,
                'user_id' => $user->id,
                'payment_id' => $paymentId,
                'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null
            ]);
            
            return $license;
        } catch (\Exception $e) {
            \Log::error('Failed to create license', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'payment_id' => $paymentId
            ]);
            
            throw $e; // Re-throw to be handled by the caller
        }
    }
    
    /**
     * Validate a license key.
     *
     * @param string $licenseKey
     * @return License|null
     */
    public function validateLicense(string $licenseKey)
    {
        $license = License::where('license_key', $licenseKey)
            ->where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->first();
            
        return $license;
    }
    
    /**
     * Revoke a license.
     *
     * @param License $license
     * @return bool
     */
    public function revokeLicense(License $license)
    {
        $license->status = 'revoked';
        return $license->save();
    }
    
    /**
     * Check if a user has an active license.
     *
     * @param User $user
     * @return bool
     */
    public function hasActiveLicense(User $user)
    {
        return License::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }
    
    /**
     * Get a user's active license.
     *
     * @param User $user
     * @return License|null
     */
    public function getActiveLicense(User $user)
    {
        try {
            // First, check for any license regardless of expiration to help with debugging
            $anyLicense = License::where('user_id', $user->id)->latest()->first();
            
            if ($anyLicense) {
                \Log::info('Found license for user', [
                    'user_id' => $user->id,
                    'license_id' => $anyLicense->id,
                    'license_key' => $anyLicense->license_key,
                    'status' => $anyLicense->status,
                    'expires_at' => $anyLicense->expires_at ? $anyLicense->expires_at->toDateTimeString() : null,
                    'is_expired' => $anyLicense->isExpired(),
                    'is_active' => $anyLicense->isActive(),
                ]);
            } else {
                \Log::info('No licenses found for user', ['user_id' => $user->id]);
            }
            
            // Now get the active license with proper criteria
            $activeLicense = License::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('expires_at', '>', Carbon::now())
                ->latest()
                ->first();
                
            if ($activeLicense) {
                \Log::info('Active license found for user', [
                    'user_id' => $user->id,
                    'license_id' => $activeLicense->id,
                    'license_key' => $activeLicense->license_key,
                    'expires_at' => $activeLicense->expires_at ? $activeLicense->expires_at->toDateTimeString() : null,
                ]);
            } else {
                \Log::info('No active license found for user', ['user_id' => $user->id]);
            }
            
            return $activeLicense;
        } catch (\Exception $e) {
            \Log::error('Error retrieving active license', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }
}
