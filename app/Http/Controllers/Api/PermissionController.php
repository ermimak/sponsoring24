<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $this->authorize('manage_users');

        return Permission::all();
    }

    public function store(Request $request)
    {
        $this->authorize('manage_users');
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'label' => 'nullable|string',
        ]);

        return Permission::create($data);
    }

    public function show(Permission $permission)
    {
        $this->authorize('manage_users');

        return $permission;
    }

    public function update(Request $request, Permission $permission)
    {
        $this->authorize('manage_users');
        $data = $request->validate([
            'name' => 'sometimes|required|string|unique:permissions,name,' . $permission->id,
            'label' => 'nullable|string',
        ]);
        $permission->update($data);

        return $permission;
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('manage_users');
        $permission->delete();

        return response()->json(['message' => 'Permission deleted']);
    }

    public function assignRole(Request $request, Permission $permission)
    {
        $this->authorize('manage_users');
        $roleId = $request->validate(['role_id' => 'required|exists:roles,id'])['role_id'];
        $permission->roles()->attach($roleId);

        return $permission->load('roles');
    }

    public function removeRole(Request $request, Permission $permission)
    {
        $this->authorize('manage_users');
        $roleId = $request->validate(['role_id' => 'required|exists:roles,id'])['role_id'];
        $permission->roles()->detach($roleId);

        return $permission->load('roles');
    }
}
