<?php

namespace App\Http\Controllers;

use App\Models\BonusCredit;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ReferralCodeUsed;
use App\Notifications\UserPendingNotification;
use App\Services\UserActivityService;
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

        // Log the successful login activity
        UserActivityService::logAuth('login', $user->id, [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'remember_me' => $request->boolean('remember')
        ]);

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
        // Log the logout activity before logging out
        if (Auth::check()) {
            UserActivityService::logAuth('logout', Auth::id(), [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }
        
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
                'registration_discount_code' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            try {
                // Create the full name from first and last name
                $fullName = $validated['contact_first_name'] . ' ' . $validated['contact_last_name'];
                
                $user = User::create([
                    'name' => $fullName,
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'approval_status' => 'pending',
                    'contact_title' => $validated['contact_title'],
                    'contact_first_name' => $validated['contact_first_name'],
                    'contact_last_name' => $validated['contact_last_name'],
                    'organization_name' => $validated['organization_name'],
                    'address' => $validated['address'],
                    'address_suffix' => $validated['address_suffix'] ?? null,
                    'postal_code' => $validated['postal_code'],
                    'location' => $validated['location'],
                    'country' => $validated['country'],
                    'phone' => $validated['phone'],
                    'newsletter' => $validated['newsletter'] ?? false,
                    'referral_code' => substr(md5(uniqid(mt_rand(), true)), 0, 10), // Generate a unique referral code
                ]);

                // Assign the user role
                $user->assignRole('user');
                
                // Create a settings record for this user
                $settings = new Setting([
                    'user_id' => $user->id,
                    'organization_name' => $validated['organization_name'],
                    'contact_title' => $validated['contact_title'],
                    'contact_first_name' => $validated['contact_first_name'],
                    'contact_last_name' => $validated['contact_last_name'],
                    'address' => $validated['address'],
                    'address_suffix' => $validated['address_suffix'] ?? null,
                    'postal_code' => $validated['postal_code'],
                    'location' => $validated['location'],
                    'country' => $validated['country'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'language' => 'English', // Default language
                    'accent_color' => '#6366F1', // Default accent color
                ]);
                $settings->save();

                // Handle referral code if present
                $referralInfo = null;
                if ($request->filled('referral_code')) {
                    $referralCode = $request->input('referral_code');
                    $referrer = User::where('referral_code', $referralCode)->first();
                    
                    if ($referrer) {
                        // Create bonus credit for referrer
                        $bonusCredit = BonusCredit::create([
                            'user_id' => $referrer->id,
                            'referred_user_id' => $user->id,
                            'amount' => 100.00,
                            'status' => 'pending',
                            'credited' => false,
                            'type' => 'referral',
                            'referral_code_used' => $referralCode,
                        ]);

                        // Mark the new user as eligible for discount
                        $user->discount_eligible = true;
                        $user->save();

                        // Send notification to referrer
                        $referrer->notify(new ReferralCodeUsed($user, $bonusCredit->amount));
                        
                        Log::info('Referral notification sent', [
                            'referrer_id' => $referrer->id,
                            'referred_user_id' => $user->id,
                            'bonus_credit_id' => $bonusCredit->id,
                            'discount_eligible' => true,
                        ]);

                        $referralInfo = [
                            'success' => true,
                            'code' => $referralCode,
                            'discount_eligible' => true,
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
                
                // Log the registration activity
                UserActivityService::logAuth('registration', $user->id, [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'referral_info' => $referralInfo
                ]);
                
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
