<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('manage_users');

        return Role::with('permissions')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('manage_users');
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'label' => 'nullable|string',
        ]);

        return Role::create($data);
    }

    public function show(Role $role)
    {
        $this->authorize('manage_users');

        return $role->load('permissions');
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('manage_users');
        $data = $request->validate([
            'name' => 'sometimes|required|string|unique:roles,name,' . $role->id,
            'label' => 'nullable|string',
        ]);
        $role->update($data);

        return $role;
    }

    public function destroy(Role $role)
    {
        $this->authorize('manage_users');
        $role->delete();

        return response()->json(['message' => 'Role deleted']);
    }

    public function assignPermission(Request $request, Role $role)
    {
        $this->authorize('manage_users');
        $permissionId = $request->validate(['permission_id' => 'required|exists:permissions,id'])['permission_id'];
        $role->permissions()->attach($permissionId);

        return $role->load('permissions');
    }

    public function removePermission(Request $request, Role $role)
    {
        $this->authorize('manage_users');
        $permissionId = $request->validate(['permission_id' => 'required|exists:permissions,id'])['permission_id'];
        $role->permissions()->detach($permissionId);

        return $role->load('permissions');
    }

    public function assignUser(Request $request, Role $role)
    {
        $this->authorize('manage_users');
        $userId = $request->validate(['user_id' => 'required|exists:users,id'])['user_id'];
        $role->users()->attach($userId);

        return $role->load('users');
    }

    public function removeUser(Request $request, Role $role)
    {
        $this->authorize('manage_users');
        $userId = $request->validate(['user_id' => 'required|exists:users,id'])['user_id'];
        $role->users()->detach($userId);

        return $role->load('users');
    }
}
