<?php

namespace App\Http\Controllers;

use App\Models\BonusCredit;
use App\Models\User;
use App\Notifications\BonusCreditNotification;
use App\Notifications\NewUserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
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
            
        // Generate a proper referral link with the 'ref' parameter
        $referralLink = route('register') . '?ref=' . ($user->referral_code ?? '');
        
        return Inertia::render('Bonus/Index', [
            'bonusCredits' => $bonusCredits,
            'referral_link' => $referralLink,
        ]);
    }

    // Called when a new user registers with a referral code
    public function registerWithReferral(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            Log::info('Referral registration request received', [
                'request_data' => $request->except(['password', 'password_confirmation']),
                'has_referral_code' => $request->has('referral_code'),
            ]);
            
            // Validate the request
            $validated = $request->validate([
                // Contact person details
                'contact_title' => 'required|string|in:Mister,Mrs,Ms',
                'contact_first_name' => 'required|string|max:100',
                'contact_last_name' => 'required|string|max:100',
                'organization_name' => 'required|string|max:255',
                
                // Address details
                'address' => 'required|string|max:255',
                'address_suffix' => 'nullable|string|max:255',
                'postal_code' => 'required|string|max:20',
                'location' => 'required|string|max:100',
                'country' => 'required|string|max:100',
                
                // Contact details
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:50',
                
                // Account credentials
                'password' => 'required|string|min:8|confirmed',
                'referral_code' => 'required|string|max:255',
            ]);
            
            $referralCode = $validated['referral_code'];
            
            // Log the attempt to use a referral code
            Log::info('Attempting to register with referral code', [
                'referral_code' => $referralCode,
                'ip_address' => $request->ip(),
            ]);
            
            // Log the referral code being used
            Log::info('Looking up referrer with code', ['referral_code' => $referralCode]);
            
            // Check if referral code exists - try both direct match and case-insensitive match
            $referrer = User::where('referral_code', $referralCode)->first();
            
            if (!$referrer) {
                // Try case-insensitive match as fallback using safe query builder methods
                $referrer = User::whereRaw('LOWER(referral_code) = LOWER(?)', [$referralCode])->first();
                
                if ($referrer) {
                    Log::info('Found referrer with case-insensitive match', [
                        'referral_code' => $referralCode,
                        'actual_code' => $referrer->referral_code,
                        'referrer_id' => $referrer->id,
                    ]);
                }
            }
            
            if (!$referrer) {
                Log::warning('Invalid referral code used during registration', [
                    'referral_code' => $referralCode,
                    'ip_address' => $request->ip(),
                ]);
                
                Session::flash('error', 'Invalid referral code. Please check and try again.');
                return redirect()->back()->withInput($request->except(['password', 'password_confirmation']));
            }
            
            // Use database transaction to ensure all operations succeed or fail together
            DB::beginTransaction();
            
            try {
                // Create user with pending status
                $userData = [
                    'name' => $validated['contact_first_name'] . ' ' . $validated['contact_last_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'approval_status' => 'pending', // Set status to pending
                    'referral_code' => Str::random(8) // Generate a referral code for the new user
                ];
                
                // Create the user
                $user = User::create($userData);
                
                // Assign the user role
                $user->assignRole('user');
                
                // Create bonus credit for referrer
                $bonusCredit = new BonusCredit();
                $bonusCredit->user_id = $referrer->id;
                $bonusCredit->referred_user_id = $user->id;
                $bonusCredit->amount = 100.00;
                $bonusCredit->status = 'pending';
                $bonusCredit->referral_code_used = $referralCode;
                $bonusCredit->type = 'referral';
                $bonusCredit->save();
                
                // Log the successful registration
                Log::info('Referral registration successful', [
                    'referrer_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'referral_code' => $referralCode,
                    'bonus_credit_id' => $bonusCredit->id,
                ]);
                
                // Notify referrer about the referral code usage
                try {
                    $referrer->notify(new BonusCreditNotification($bonusCredit, 'referral_used', [
                        'referred_name' => $user->name,
                        'referred_email' => $user->email,
                        'currency' => 'CHF',
                        'referral_code' => $referralCode
                    ]));
                    
                    Log::info('Referrer notification sent successfully', [
                        'referrer_id' => $referrer->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send referrer notification', [
                        'error' => $e->getMessage(),
                        'referrer_id' => $referrer->id
                    ]);
                }
                
                // Notify admins about new user registration requiring approval
                try {
                    // Find super-admin users
                    $admins = User::whereHas('roles', function($query) {
                        $query->where('name', 'super-admin');
                    })->get();
                    
                    Log::info('Found ' . $admins->count() . ' super-admin users to notify');
                    
                    foreach ($admins as $admin) {
                        $admin->notify(new NewUserRegistration($user));
                        Log::info('Admin notification sent', ['admin_id' => $admin->id]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send admin notifications', [
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Commit the transaction
                DB::commit();
                
                // Store success message in session
                Session::flash('success', 'Registration successful! Your account requires admin approval before you can log in. You will be notified by email once approved.');
                
                return redirect()->route('login')
                    ->with('status', 'Your account has been created and is pending approval by an administrator.');
                    
            } catch (\Exception $innerException) {
                // Roll back the transaction if anything fails
                DB::rollBack();
                Log::error('Failed to register user with referral', [
                    'error' => $innerException->getMessage(),
                    'trace' => $innerException->getTraceAsString(),
                    'referral_code' => $referralCode
                ]);
                throw $innerException;
            }
        
        } catch (\Exception $e) {
            Log::error('Error processing referral registration', [
                'error' => $e->getMessage(),
                'referral_code' => $referralCode ?? null,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);
            
            // If we have a validation error, return with the errors
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput($request->except(['password', 'password_confirmation']));
            }
            
            // For other errors, show a generic message
            Session::flash('error', 'There was an error processing your registration with referral code. Please try again later.');
            return redirect()->back()->withInput($request->except(['password', 'password_confirmation']));
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
                    'referrer_name' => $referrer ? $referrer->name : 'A Sponsoring24 user'
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
