<?php

namespace App\Http\Controllers;

use App\Models\BonusCredit;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class BonusCreditController extends Controller
{
    // Show the current user's bonus credits
    public function index(Request $request)
    {
        $user = $request->user();
        $bonusCredits = BonusCredit::where('user_id', $user->id)
            ->with('referredUser')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('Bonus/Index', [
            'bonusCredits' => $bonusCredits,
            'referral_link' => route('register', ['registration_discount_code' => $user->referral_code ?? 'C3BE7A'])
        ]);
    }

    // Called when a new user registers with a referral code
    public function registerWithReferral(Request $request)
    {
        try {
            $referralCode = $request->input('registration_discount_code');
            $referrer = User::where('referral_code', $referralCode)->first();

            if ($referrer) {
                // Create the user (simplified, real logic may differ)
                $user = User::create($request->only(['name', 'email', 'password']));
                
                // Create bonus credit for referrer
                $bonusCredit = BonusCredit::create([
                    'user_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'amount' => 100.00,
                    'status' => 'pending',
                    'referral_code_used' => $referralCode,
                ]);

                // Log the successful referral
                Log::info('Referral code used successfully', [
                    'referrer_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'referral_code' => $referralCode,
                    'bonus_credit_id' => $bonusCredit->id
                ]);

                // Store success message in session
                Session::flash('success', 'Referral code applied successfully! You will receive bonus credits once your account is verified.');
                
                // Notify referrer (you can implement your notification system here)
                // Example: $referrer->notify(new ReferralCodeUsed($user));
            } else {
                Log::warning('Invalid referral code used', [
                    'referral_code' => $referralCode,
                    'ip_address' => $request->ip()
                ]);
                
                Session::flash('error', 'Invalid referral code. Please check and try again.');
            }

            // Continue registration flow...
            return redirect()->route('dashboard')->with('referral_status', $referrer ? 'success' : 'error');
            
        } catch (\Exception $e) {
            Log::error('Error processing referral code', [
                'error' => $e->getMessage(),
                'referral_code' => $referralCode ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            Session::flash('error', 'There was an error processing your referral code. Please try again later.');
            return redirect()->back();
        }
    }

    // Mark bonus as credited (e.g., after project completion)
    public function creditBonus(BonusCredit $bonusCredit)
    {
        try {
            $bonusCredit->update(['status' => 'credited']);
            
            // Log the credit
            Log::info('Bonus credit marked as credited', [
                'bonus_credit_id' => $bonusCredit->id,
                'user_id' => $bonusCredit->user_id,
                'referred_user_id' => $bonusCredit->referred_user_id,
                'amount' => $bonusCredit->amount
            ]);

            // Optionally, trigger invoice logic here
            return back()->with('success', 'Bonus credit has been successfully credited.');
        } catch (\Exception $e) {
            Log::error('Error crediting bonus', [
                'error' => $e->getMessage(),
                'bonus_credit_id' => $bonusCredit->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'There was an error crediting the bonus. Please try again.');
        }
    }
} 