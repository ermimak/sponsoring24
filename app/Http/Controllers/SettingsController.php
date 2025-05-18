<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        try {
            Log::info('Loading settings page for user: ' . (Auth::check() ? Auth::id() : 'unauthenticated'));
            if (!Auth::check()) {
                Log::warning('User not authenticated, redirecting to login.');
                return redirect()->route('login')->with('error', 'Please log in to access settings.');
            }

            $settings = Setting::firstOrNew([]);
            Log::info('Settings loaded: ' . json_encode($settings->toArray()));
            return Inertia::render('Settings', [
                'settings' => [
                    'organization_name' => $settings->organization_name,
                    'contact_title' => $settings->contact_title,
                    'contact_first_name' => $settings->contact_first_name,
                    'contact_last_name' => $settings->contact_last_name,
                    'address' => $settings->address,
                    'address_suffix' => $settings->address_suffix,
                    'postal_code' => $settings->postal_code,
                    'location' => $settings->location,
                    'country' => $settings->country,
                    'language' => $settings->language,
                    'email' => $settings->email,
                    'phone' => $settings->phone,
                    'accent_color' => $settings->accent_color,
                    'logo_path' => $settings->logo_path,
                    'billing_salutation' => $settings->billing_salutation,
                    'billing_first_name' => $settings->billing_first_name,
                    'billing_last_name' => $settings->billing_last_name,
                    'billing_address' => $settings->billing_address,
                    'billing_address_suffix' => $settings->billing_address_suffix,
                    'billing_postal_code' => $settings->billing_postal_code,
                    'billing_location' => $settings->billing_location,
                    'billing_country' => $settings->billing_country,
                    'billing_email' => $settings->billing_email,
                    'billing_phone' => $settings->billing_phone,
                    'bank_account_details' => $settings->bank_account_details,
                    'iban' => $settings->iban,
                    'recipient' => $settings->recipient,
                    'project_overview_enabled' => $settings->project_overview_enabled,
                    'user' => $settings->user ? [
                        'id' => $settings->user->id,
                        'name' => $settings->user->name,
                        'email' => $settings->user->email,
                    ] : null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load settings: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'An error occurred while loading settings. Please try again.')->withInput();
        }
    }

    public function update(Request $request)
    {
        try {
            Log::info('Updating settings for user: ' . Auth::id(), ['request_data' => $request->all()]);
            $settings = Setting::first() ?? new Setting();

            $validated = $request->validate([
                'organization_name' => 'required|string|max:255',
                'contact_title' => 'required|in:Mister,Mrs,Ms',
                'contact_first_name' => 'required|string|max:255',
                'contact_last_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:10',
                'location' => 'required|string|max:100',
                'country' => 'required|string|max:100',
                'language' => 'required|in:German,English,French,Italian',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'billing_salutation' => 'nullable|in:Mister,Mrs,Ms',
                'billing_first_name' => 'nullable|string|max:255',
                'billing_last_name' => 'nullable|string|max:255',
                'billing_address' => 'nullable|string|max:255',
                'billing_address_suffix' => 'nullable|string|max:255',
                'billing_postal_code' => 'nullable|string|max:10',
                'billing_location' => 'nullable|string|max:100',
                'billing_country' => 'nullable|string|max:100',
                'billing_email' => 'nullable|email|max:255',
                'billing_phone' => 'nullable|string|max:20',
                'bank_account_details' => 'nullable|string',
                'iban' => 'nullable|string|max:34',
                'recipient' => 'nullable|string|max:255',
                'project_overview_enabled' => 'required|boolean',
                'logo' => 'nullable|image|max:2048',
                'password' => 'nullable|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'password_confirmation' => 'nullable|same:password',
            ]);

            $data = $request->only([
                'organization_name', 'contact_title', 'contact_first_name', 'contact_last_name',
                'address', 'address_suffix', 'postal_code', 'location', 'country', 'language',
                'email', 'phone', 'accent_color', 'billing_salutation', 'billing_first_name',
                'billing_last_name', 'billing_address', 'billing_address_suffix', 'billing_postal_code',
                'billing_location', 'billing_country', 'billing_email', 'billing_phone', 'bank_account_details',
                'iban', 'recipient', 'project_overview_enabled',
            ]);

            if ($request->hasFile('logo')) {
                if ($settings->logo_path) {
                    Storage::delete($settings->logo_path);
                }
                $data['logo_path'] = $request->file('logo')->store('logos', 'public');
            }

            if ($request->filled('password')) {
                $user = Auth::user();
                Log::info('Attempting to update password for user: ' . $user->id, ['new_password' => $request->password]);
                $user->update(['password' => bcrypt($request->password)]);
                Log::info('Password updated successfully for user: ' . $user->id);
            }

            $data['user_id'] = Auth::id();
            $settings->update($data);

            // Propagate relevant settings to related projects
            $projectFields = [
                'organization_name',
                'accent_color',
                'logo_path',
                'country',
                'language',
            ];

            $changed = false;
            foreach ($projectFields as $field) {
                if (array_key_exists($field, $data) && $settings->$field !== $data[$field]) {
                    $changed = true;
                    break;
                }
            }

            if ($changed) {
                foreach ($settings->projects as $project) {
                    foreach ($projectFields as $field) {
                        if (array_key_exists($field, $data)) {
                            $project->$field = $data[$field];
                        }
                    }
                    $project->save();
                }
            }

            Log::info('Settings updated successfully for user: ' . Auth::id());
            return redirect()->back()->with('success', 'Settings updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for settings update: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update settings: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Failed to update settings.')->withInput();
        }
    }
}