<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenseApiController extends Controller
{
    protected $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Check the license status for the current user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated',
                'has_active_license' => false
            ], 401);
        }
        
        // Check if user has an active license
        $hasActiveLicense = $this->licenseService->hasActiveLicense($user);
        
        // Get all licenses for the user
        $licenses = License::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get the active license if it exists
        $activeLicense = $this->licenseService->getActiveLicense($user);
        
        return response()->json([
            'user_id' => $user->id,
            'has_active_license' => $hasActiveLicense,
            'license_count' => $licenses->count(),
            'license' => $activeLicense,
            'all_licenses' => $licenses,
            'checked_at' => now()->toDateTimeString()
        ]);
    }
}
