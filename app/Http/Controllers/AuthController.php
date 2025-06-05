<?php

namespace App\Http\Controllers;

use App\Models\BonusCredit;
use App\Models\User;
use App\Notifications\ReferralCodeUsed;
use App\Notifications\UserPendingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => [trans('auth.failed')],
                ]);
            }

            return back()->withErrors([
                'email' => trans('auth.failed'),
            ]);
        }
        
        // Check if user is approved
        if ($user->approval_status !== 'approved') {
            $message = 'Your account is pending approval by an administrator.';
            
            if ($user->approval_status === 'rejected') {
                $message = 'Your account registration has been rejected. Reason: ' . ($user->rejection_reason ?: 'Not specified');
            }
            
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => [$message],
                ]);
            }
            
            return back()->withErrors([
                'email' => $message,
            ]);
        }

        // Log in the user
        Auth::login($user, $request->boolean('remember'));

        // Regenerate session
        $request->session()->regenerate();

        // Debug logging
        Log::info('User logged in', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => $user->permissions->pluck('name')->toArray(), // Use Spatie's relationship
                'roles' => $user->getRoleNames()->toArray(),
            ],
            'session' => $request->session()->all(),
            'auth' => Auth::check(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'permissions' => $user->permissions->pluck('name')->toArray(), // Use Spatie's relationship
                    'roles' => $user->getRoleNames()->toArray(),
                ],
            ]);
        }
        
        // Redirect super admin users to admin dashboard
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Super Admin!');
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Login successful');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out']);
        }

        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'registration_discount_code' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            try {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'approval_status' => 'pending',
                ]);

                // Assign the user role
                $user->assignRole('user');

                // Handle referral code if present
                $referralInfo = null;
                if ($request->filled('registration_discount_code')) {
                    $referralCode = $request->input('registration_discount_code');
                    $referrer = User::where('referral_code', $referralCode)->first();
                    
                    if ($referrer) {
                        // Create bonus credit for referrer
                        $bonusCredit = BonusCredit::create([
                            'user_id' => $referrer->id,
                            'referred_user_id' => $user->id,
                            'amount' => 100.00,
                            'status' => 'pending',
                            'referral_code_used' => $referralCode,
                        ]);

                        // Send notification to referrer
                        $referrer->notify(new ReferralCodeUsed($user, $bonusCredit->amount));
                        
                        Log::info('Referral notification sent', [
                            'referrer_id' => $referrer->id,
                            'referred_user_id' => $user->id,
                            'bonus_credit_id' => $bonusCredit->id,
                        ]);

                        $referralInfo = [
                            'success' => true,
                            'code' => $referralCode,
                        ];
                    } else {
                        $referralInfo = [
                            'success' => false,
                            'code' => $referralCode,
                            'message' => 'Invalid referral code',
                        ];
                    }
                }

                // Send pending notification to the user
                $user->notify(new UserPendingNotification());
                
                DB::commit();
                
                // Don't log in the user automatically, they need approval first

                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Registration successful. Your account is pending approval by an administrator.',
                        'referralInfo' => $referralInfo,
                    ]);
                }

                return redirect()->route('home')
                    ->with('success', 'Registration successful. Your account is pending approval by an administrator.')
                    ->with('referralInfo', $referralInfo);
            } catch (\Exception $e) {
                DB::rollBack();

                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password', 'password_confirmation']),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Registration failed',
                    'error' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Registration failed. Please try again.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }
}
