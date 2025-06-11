<?php

namespace App\Http\Controllers;

use App\Models\BonusCredit;
use App\Models\User;
use App\Notifications\BonusCreditNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

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
            'referral_link' => route('register', ['registration_discount_code' => $user->referral_code ?? 'C3BE7A']),
        ]);
    }

    // Called when a new user registers with a referral code
    public function registerWithReferral(Request $request)
    {
        try {
            $referralCode = $request->input('registration_discount_code');
            $referrer = User::where('referral_code', $referralCode)->first();

            if ($referrer) {
                // Create the user with pending status to require admin approval
                $userData = $request->only(['name', 'email', 'password']);
                $userData['status'] = 'pending'; // Ensure user requires admin approval
                $user = User::create($userData);
                
                // Create bonus credit for referrer
                $bonusCredit = BonusCredit::create([
                    'user_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'amount' => 100.00,
                    'status' => 'pending',
                    'referral_code_used' => $referralCode,
                    'type' => 'referral', // Add type field
                ]);

                // Log the successful referral
                Log::info('Referral code used successfully', [
                    'referrer_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'referral_code' => $referralCode,
                    'bonus_credit_id' => $bonusCredit->id,
                ]);

                // Store success message in session
                Session::flash('success', 'Registration successful! Your account requires admin approval before you can log in. You will be notified by email once approved.');
                
                // Notify referrer about the referral code usage
                $referrer->notify(new BonusCreditNotification($bonusCredit, 'referral_used', [
                    'referred_name' => $user->name,
                    'referred_email' => $user->email,
                    'currency' => 'CHF',
                    'referral_code' => $referralCode
                ]));
                
                // Notify admin about new user registration requiring approval
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\NewUserRegistration($user));
                }
                
                return redirect()->route('login')->with('status', 'Your account has been created and is pending approval by an administrator.');
            } else {
                Log::warning('Invalid referral code used', [
                    'referral_code' => $referralCode,
                    'ip_address' => $request->ip(),
                ]);
                
                Session::flash('error', 'Invalid referral code. Please check and try again.');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::error('Error processing referral code', [
                'error' => $e->getMessage(),
                'referral_code' => $referralCode ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            
            Session::flash('error', 'There was an error processing your referral code. Please try again later.');
            return redirect()->back();
        }
    }

    // Mark bonus as credited (e.g., after project completion or license purchase)
    public function creditBonus(BonusCredit $bonusCredit)
    {
        try {
            $bonusCredit->update(['status' => 'credited']);
            
            // Log the credit
            Log::info('Bonus credit marked as credited', [
                'bonus_credit_id' => $bonusCredit->id,
                'user_id' => $bonusCredit->user_id,
                'referred_user_id' => $bonusCredit->referred_user_id,
                'amount' => $bonusCredit->amount,
                'referral_code_used' => $bonusCredit->referral_code_used,
            ]);
            
            // Load the user who receives the bonus credit (the referrer)
            $referrer = User::find($bonusCredit->user_id);
            if ($referrer) {
                // Notify the referrer that their bonus credit has been processed
                $referrer->notify(new BonusCreditNotification($bonusCredit, 'credited', [
                    'currency' => 'CHF',
                    'processed_at' => now()->toDateTimeString(),
                    'amount' => $bonusCredit->amount
                ]));
                
                // Here you would typically add the credit to the user's account balance
                // This could be implemented as a separate table or field in the users table
                // For now, we'll just log it
                Log::info('Referral bonus credited to user', [
                    'user_id' => $referrer->id,
                    'user_email' => $referrer->email,
                    'amount' => $bonusCredit->amount,
                    'currency' => 'CHF',
                ]);
            }
            
            // Load the referred user to check their status
            $referredUser = User::find($bonusCredit->referred_user_id);
            if ($referredUser) {
                // Ensure the referred user is marked as eligible for discount
                if (!$referredUser->discount_eligible) {
                    $referredUser->discount_eligible = true;
                    $referredUser->save();
                    
                    Log::info('User marked as discount eligible', [
                        'user_id' => $referredUser->id,
                        'user_email' => $referredUser->email,
                        'discount_eligible' => true,
                    ]);
                }
                
                // Notify the referred user about their discount eligibility
                $referredUser->notify(new BonusCreditNotification($bonusCredit, 'discount_eligible', [
                    'currency' => 'CHF',
                    'discount_amount' => 50.00,
                    'referrer_name' => $referrer ? $referrer->name : 'A Fundoo user'
                ]));
            }
            
            // Notify admin users about the bonus credit processing
            $admins = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new BonusCreditNotification($bonusCredit, 'credited', [
                    'currency' => 'CHF',
                    'processed_at' => now()->toDateTimeString(),
                    'amount' => $bonusCredit->amount,
                    'user_name' => $referrer ? $referrer->name : 'Unknown User',
                    'user_email' => $referrer ? $referrer->email : 'Unknown Email',
                    'referred_user_name' => $referredUser ? $referredUser->name : 'Unknown User',
                    'referred_user_email' => $referredUser ? $referredUser->email : 'Unknown Email'
                ]));
            }

            // Optionally, trigger invoice logic here
            return back()->with('success', 'Bonus credit has been successfully credited.');
        } catch (\Exception $e) {
            Log::error('Error crediting bonus', [
                'error' => $e->getMessage(),
                'bonus_credit_id' => $bonusCredit->id,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', 'There was an error crediting the bonus. Please try again.');
        }
    }
}
