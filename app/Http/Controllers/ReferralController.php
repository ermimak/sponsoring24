<?php

namespace App\Http\Controllers;

use App\Models\BonusCredit;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReferralController extends Controller
{
    /**
     * Display the referrals dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get all referrals (users who signed up using this user's referral code)
        $referrals = BonusCredit::with('referredUser')
            ->where('user_id', $user->id)
            ->where('type', 'referral')
            ->get()
            ->map(function ($bonusCredit) {
                return [
                    'id' => $bonusCredit->id,
                    'user' => [
                        'name' => $bonusCredit->referredUser->name,
                        'email' => $bonusCredit->referredUser->email,
                    ],
                    'created_at' => $bonusCredit->created_at,
                    'status' => $bonusCredit->credited ? 'credited' : 'pending',
                    'amount' => 100.00, // CHF 100 per referral
                ];
            });
        
        // Calculate statistics
        $stats = [
            'totalReferrals' => $referrals->count(),
            'totalEarned' => $referrals->where('status', 'credited')->sum('amount'),
            'conversionRate' => $referrals->count() > 0
                ? round(($referrals->where('status', 'credited')->count() / $referrals->count()) * 100)
                : 0,
        ];
        
        return Inertia::render('Dashboard/Referrals', [
            'user' => $user,
            'referrals' => $referrals,
            'stats' => $stats,
        ]);
    }
}
