<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BonusCredit;
use App\Models\User;
use App\Models\License;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReferralManagementController extends Controller
{
    /**
     * Display the admin referral management dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Get all referrals with their users
        $referrals = BonusCredit::with(['user', 'referredUser'])
            ->where('type', 'referral')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Get referral statistics
        $stats = [
            'totalReferrals' => BonusCredit::where('type', 'referral')->count(),
            'totalCredited' => BonusCredit::where('type', 'referral')->where('status', 'credited')->count(),
            'totalPending' => BonusCredit::where('type', 'referral')->where('status', 'pending')->count(),
            'totalAmount' => BonusCredit::where('type', 'referral')->where('status', 'credited')->sum('amount'),
        ];
        
        // Get top referrers
        $topReferrers = User::withCount(['bonusCredits' => function($query) {
                $query->where('type', 'referral');
            }])
            ->orderBy('bonus_credits_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'referral_count' => $user->bonus_credits_count,
                    'total_earned' => BonusCredit::where('user_id', $user->id)
                        ->where('type', 'referral')
                        ->where('status', 'credited')
                        ->sum('amount'),
                ];
            });
            
        // Get monthly referral trends
        $monthlyTrends = BonusCredit::where('type', 'referral')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
            
        return Inertia::render('Admin/ReferralManagement', [
            'referrals' => $referrals,
            'stats' => $stats,
            'topReferrers' => $topReferrers,
            'monthlyTrends' => $monthlyTrends,
        ]);
    }
    
    /**
     * Update the status of a referral bonus credit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BonusCredit  $bonusCredit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, BonusCredit $bonusCredit)
    {
        $request->validate([
            'status' => 'required|in:pending,credited,rejected',
        ]);
        
        $bonusCredit->update([
            'status' => $request->status,
        ]);
        
        if ($request->status === 'credited') {
            // Notify the user that their referral bonus has been credited
            $bonusCredit->user->notify(new \App\Notifications\ReferralBonusNotification(
                $bonusCredit,
                'referral_credited',
                [
                    'amount' => $bonusCredit->amount,
                    'currency' => 'CHF',
                    'referred_name' => $bonusCredit->referredUser->name,
                ]
            ));
        }
        
        return redirect()->back()->with('success', 'Referral status updated successfully.');
    }
    
    /**
     * Display the discount management page.
     *
     * @return \Inertia\Response
     */
    public function discounts()
    {
        // Get all licenses with applied discounts
        $discountedLicenses = License::with('user')
            ->where('discount_applied', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Get discount statistics
        $stats = [
            'totalDiscounts' => License::where('discount_applied', true)->count(),
            'totalDiscountAmount' => License::where('discount_applied', true)->sum('discount_amount'),
            'averageDiscount' => License::where('discount_applied', true)->avg('discount_amount'),
        ];
        
        return Inertia::render('Admin/DiscountManagement', [
            'discountedLicenses' => $discountedLicenses,
            'stats' => $stats,
        ]);
    }
}
