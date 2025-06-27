<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserActivityService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotPassword()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            // Check if user exists
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return back()->withErrors([
                    'email' => __('We can\'t find a user with that email address.'),
                ]);
            }

            // Send the password reset link
            $status = Password::sendResetLink(
                $request->only('email')
            );

            // Log the password reset request
            UserActivityService::logAuth('password_reset_requested', $user->id, [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
        } catch (\Exception $e) {
            Log::error('Password reset link sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->only('email'),
            ]);

            return back()->withErrors([
                'email' => 'An error occurred while sending the password reset link. Please try again.',
            ]);
        }
    }

    /**
     * Show the password reset form
     */
    public function showResetForm(Request $request, $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Attempt to reset the user's password
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) use ($request) {
                    DB::beginTransaction();
                    try {
                        // Update the password
                        $user->forceFill([
                            'password' => Hash::make($password),
                            'remember_token' => Str::random(60),
                        ])->save();

                        // Log the password reset
                        UserActivityService::logAuth('password_reset_completed', $user->id, [
                            'ip' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ]);

                        DB::commit();
                        
                        // Fire the password reset event
                        event(new PasswordReset($user));
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                }
            );

            // Return the status
            return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => __($status)]);
        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password', 'password_confirmation']),
            ]);

            return back()->withErrors([
                'email' => 'An error occurred while resetting your password. Please try again.',
            ]);
        }
    }
}
