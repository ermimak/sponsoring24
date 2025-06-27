<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LicenseController extends Controller
{
    protected $licenseService;
    
    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Display a listing of all licenses.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        try {
            $query = License::with('user')
                ->select('licenses.*')
                ->orderBy('created_at', 'desc');
            
            // Apply filters if provided
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('license_key', 'like', "%{$search}%")
                      ->orWhere('payment_id', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }
            
            $licenses = $query->paginate(15)
                ->through(function ($license) {
                    // Add additional computed properties
                    $license->is_active = $license->isActive();
                    $license->is_expired = $license->isExpired();
                    $license->days_remaining = $license->daysRemaining();
                    return $license;
                });
            
            // Get summary statistics
            $stats = [
                'total' => License::count(),
                'active' => License::where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->count(),
                'expired' => License::where(function($query) {
                    $query->where('status', 'expired')
                        ->orWhere(function($q) {
                            $q->where('status', 'active')
                              ->where('expires_at', '<=', now());
                        });
                })->count(),
                'revoked' => License::where('status', 'revoked')->count(),
                'revenue' => License::sum('amount'),
                'discounts_applied' => License::where('discount_applied', true)->count(),
                'discount_total' => License::where('discount_applied', true)->sum('discount_amount'),
            ];
            
            return Inertia::render('Admin/Licenses/Index', [
                'licenses' => $licenses,
                'stats' => $stats,
                'filters' => $request->only(['status', 'search']),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in admin license index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return Inertia::render('Admin/Licenses/Index', [
                'licenses' => [],
                'stats' => [],
                'filters' => $request->only(['status', 'search']),
                'error' => 'An error occurred while loading licenses. Please try again later.'
            ]);
        }
    }
    
    /**
     * Display the specified license.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        try {
            $license = License::with('user')->findOrFail($id);
            
            // Add computed properties
            $license->is_active = $license->isActive();
            $license->is_expired = $license->isExpired();
            $license->days_remaining = $license->daysRemaining();
            
            // Get user activity related to this license
            $activities = DB::table('activities')
                ->where('subject_type', 'App\\Models\\License')
                ->where('subject_id', $license->id)
                ->orWhere(function($query) use ($license) {
                    $query->where('causer_type', 'App\\Models\\User')
                          ->where('causer_id', $license->user_id)
                          ->where('description', 'like', '%license%');
                })
                ->orderBy('created_at', 'desc')
                ->get();
            
            return Inertia::render('Admin/Licenses/Show', [
                'license' => $license,
                'activities' => $activities,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in admin license show', [
                'license_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('admin.licenses.index')
                ->with('error', 'License not found or an error occurred.');
        }
    }
    
    /**
     * Update the specified license status.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,expired,revoked',
            ]);
            
            $license = License::findOrFail($id);
            
            // Use a transaction for ACID compliance
            DB::beginTransaction();
            
            $license->status = $request->status;
            $license->save();
            
            // Log the status change
            activity()
                ->performedOn($license)
                ->causedBy($request->user())
                ->withProperties(['old_status' => $license->getOriginal('status'), 'new_status' => $request->status])
                ->log('License status updated by admin');
            
            DB::commit();
            
            return redirect()->back()->with('success', 'License status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating license status', [
                'license_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->with('error', 'Failed to update license status.');
        }
    }
}
