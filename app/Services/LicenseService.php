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
        $expiresAt = null;
        
        // Set expiration date based on license type
        if ($type === 'annual') {
            $expiresAt = Carbon::now()->addYear();
        } elseif ($type === 'monthly') {
            $expiresAt = Carbon::now()->addMonth();
        }
        
        // Generate a unique license key
        $licenseKey = $this->generateLicenseKey();
        
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
        
        return $license;
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
        return License::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();
    }
}
