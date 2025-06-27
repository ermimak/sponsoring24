<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create permissions
        $permissions = [
            'view_dashboard',
            'manage_projects',
            'manage_users',
            'manage_roles',
            'manage_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $userRole->givePermissionTo(['view_dashboard']);
        $adminRole->givePermissionTo($permissions);
    }
}
