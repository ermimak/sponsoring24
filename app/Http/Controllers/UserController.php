<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserActionNotification;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('roles', 'permissions')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'permissions' => $user->permissions->pluck('name')->toArray(),
                ];
            });

            Log::info('User list retrieved successfully', ['user_count' => $users->count()]);

            return Inertia::render('User/UserManagement', [
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user list', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Error', [
                'message' => 'An error occurred while loading users.',
            ])->toResponse(request())->setStatusCode(500);
        }
    }

    public function create()
    {
        try {
            $roles = Role::all()->map(function ($role) {
                return ['id' => $role->id, 'name' => $role->name];
            });
            $permissions = Permission::all()->map(function ($permission) {
                return ['id' => $permission->id, 'name' => $permission->name];
            });

            Log::info('User creation form loaded', ['role_count' => count($roles), 'permission_count' => count($permissions)]);

            return Inertia::render('User/UserCreateEdit', [
                'roles' => $roles,
                'permissions' => $permissions,
                'user' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load user creation form', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Error', [
                'message' => 'An error occurred while loading the user creation form.',
            ])->toResponse(request())->setStatusCode(500);
        }
    }

    public function edit($id)
    {
        try {
            $user = User::with(['roles', 'permissions', 'setting'])->findOrFail($id);
            $roles = Role::all()->map(function ($role) {
                return ['id' => $role->id, 'name' => $role->name];
            });
            $permissions = Permission::all()->map(function ($permission) {
                return ['id' => $permission->id, 'name' => $permission->name];
            });

            Log::info('User edit form loaded', ['user_id' => $user->id, 'name' => $user->name]);

            return Inertia::render('User/UserCreateEdit', [
                'roles' => $roles,
                'permissions' => $permissions,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'two_factor_enabled' => $user->setting?->two_factor_enabled ?? false,
                    'roles' => $user->roles->pluck('id')->toArray(),
                    'permissions' => $user->permissions->pluck('id')->toArray(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load user edit form', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $id,
            ]);

            return Inertia::render('Error', [
                'message' => 'An error occurred while loading the user edit form.',
            ])->toResponse(request())->setStatusCode(500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($request->user_id ?: 'NULL')],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'two_factor' => ['boolean'],
                'roles' => ['array'],
                'permissions' => ['array'],
            ]);

            $user = $request->user_id
                ? User::findOrFail($request->user_id)
                : new User();

            $user->name = $validated['first_name'] . ' ' . $validated['last_name'];
            $user->email = $validated['email'];
            $user->password = bcrypt($validated['password']);
            $user->save();
            
            // Log user profile update activity
            $isNewUser = !$request->user_id;
            $activityType = $isNewUser ? 'user_created' : 'profile_updated';
            UserActivityService::logProfileUpdate($activityType, [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'updated_by' => Auth::id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'fields_updated' => array_keys($validated)
            ], Auth::id());
            
            // Send notification to admin users about user creation/update
            $action = $isNewUser ? 'created' : 'updated';
            $admins = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })->get();
            
            foreach ($admins as $admin) {
                if ($admin->id !== Auth::id()) { // Don't notify the admin who made the change
                    $admin->notify(new UserActionNotification($user, $action, [
                        'updated_fields' => array_keys($validated),
                        'updated_by' => Auth::user()->name
                    ]));
                }
            }

            // Handle settings with default values
            if ($user->setting) {
                $user->setting->update([
                    'two_factor_enabled' => $validated['two_factor'] ?? false,
                    'accent_color' => '#9500FF',
                    'country' => 'Switzerland',
                    'language' => 'German',
                    'billing_country' => 'Switzerland',
                    'billing_last_name' => $validated['last_name'],
                    'billing_address_suffix' => '',
                ]);
            } else {
                $user->setting()->create([
                    'two_factor_enabled' => $validated['two_factor'] ?? false,
                    'accent_color' => '#9500FF',
                    'country' => 'Switzerland',
                    'language' => 'German',
                    'billing_country' => 'Switzerland',
                    'billing_last_name' => $validated['last_name'],
                    'billing_address_suffix' => '',
                ]);
            }

            // Store previous roles and permissions for activity logging
            $previousRoles = $user->roles->pluck('name')->toArray();
            $previousPermissions = $user->permissions->pluck('name')->toArray();
            
            // Sync roles and permissions
            $user->syncRoles($request->roles ?? []);
            $user->syncPermissions($request->permissions ?? []);
            
            // Log role and permission changes
            $newRoles = $user->roles()->get()->pluck('name')->toArray();
            $newPermissions = $user->permissions()->get()->pluck('name')->toArray();
            
            // Only log if there were changes
            if ($previousRoles != $newRoles || $previousPermissions != $newPermissions) {
                UserActivityService::logAdmin('user_roles_permissions_updated', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'previous_roles' => $previousRoles,
                    'new_roles' => $newRoles,
                    'previous_permissions' => $previousPermissions,
                    'new_permissions' => $newPermissions,
                    'updated_by' => Auth::id(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ], Auth::id());
                
                // Send notification about role change
                $admins = User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['admin', 'super-admin']);
                })->get();
                
                foreach ($admins as $admin) {
                    if ($admin->id !== Auth::id()) { // Don't notify the admin who made the change
                        $admin->notify(new UserActionNotification($user, 'role_changed', [
                            'previous_roles' => $previousRoles,
                            'roles' => $newRoles,
                            'updated_by' => Auth::user()->name
                        ]));
                    }
                }
                
                // Notify the user whose roles were changed
                if ($user->id !== Auth::id()) {
                    $user->notify(new UserActionNotification($user, 'role_changed', [
                        'roles' => $newRoles,
                        'updated_by' => Auth::user()->name
                    ]));
                }
            }

            Log::info('User ' . ($request->user_id ? 'updated' : 'created') . ' successfully', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $request->roles ?? [],
                'permissions' => $request->permissions ?? [],
            ]);

            return redirect()->route('dashboard.users')->with('success', 'User ' . ($request->user_id ? 'updated' : 'created') . ' successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed while ' . ($request->user_id ? 'updating' : 'creating') . ' user', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to ' . ($request->user_id ? 'update' : 'create') . ' user', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred. Please try again.'])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            // Delete user's settings if they exist
            if ($user->setting) {
                $user->setting->delete();
            }

            // Store user info before deletion for logging
            $userInfo = [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'roles' => $user->roles->pluck('name')->toArray(),
                'permissions' => $user->permissions->pluck('name')->toArray(),
                'deleted_by' => Auth::id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ];
            
            // Store user data for notifications before deletion
            $userCopy = clone $user;
            
            // Delete the user
            $user->delete();
            
            // Log user deletion activity
            UserActivityService::logAdmin('user_deleted', $userInfo, Auth::id());
            
            // Send notification to admin users about user deletion
            $admins = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })->get();
            
            foreach ($admins as $admin) {
                if ($admin->id !== Auth::id()) { // Don't notify the admin who made the change
                    $admin->notify(new UserActionNotification($userCopy, 'deleted', [
                        'deleted_by' => Auth::user()->name,
                        'deleted_at' => now()->toDateTimeString(),
                        'roles' => $userInfo['roles']
                    ]));
                }
            }

            Log::info('User deleted successfully', [
                'user_id' => $id,
                'deleted_by' => auth()->id(),
            ]);

            return redirect()->route('dashboard.users')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete user', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $id,
            ]);

            return redirect()->back()->with('error', 'An error occurred while deleting the user.');
        }
    }
}
