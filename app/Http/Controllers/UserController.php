<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

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
            $user = User::with('roles', 'permissions')->findOrFail($id);
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
                    'two_factor_enabled' => $user->two_factor_enabled,
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
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($request->user_id ?? '')],
                'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
                'two_factor' => ['boolean'],
                'roles' => ['array'],
                'permissions' => ['array'],
            ]);

            $user = $request->user_id
                ? User::findOrFail($request->user_id)
                : new User();

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if ($validated['password']) {
                $user->password = bcrypt($validated['password']);
            }
            $user->two_factor_enabled = $validated['two_factor'] ?? false;
            $user->save();

            $user->syncRoles($request->roles ?? []);
            $user->syncPermissions($request->permissions ?? []);

            Log::info('User ' . ($request->user_id ? 'updated' : 'created') . ' successfully', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $request->roles ?? [],
                'permissions' => $request->permissions ?? [],
            ]);

            return redirect()->route('dashboard.users')->with('success', 'User ' . ($request->user_id ? 'updated' : 'created') . ' successfully');
        } catch (\Exception $e) {
            Log::error('Failed to ' . ($request->user_id ? 'update' : 'create') . ' user', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return redirect()->back()->withErrors(['error' => 'An error occurred while ' . ($request->user_id ? 'updating' : 'creating') . ' the user.'])->withInput();
        }
    }
}