<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = [
            // Project permissions
            'manage_projects', 'view_projects', 'create_projects', 'edit_projects', 'delete_projects',
            // Donation permissions
            'manage_donations', 'view_donations', 'create_donations', 'edit_donations', 'delete_donations',
            // Sponsorship run permissions
            'manage_sponsorship_runs', 'view_sponsorship_runs', 'create_sponsorship_runs', 'edit_sponsorship_runs', 'delete_sponsorship_runs',
            // Report permissions
            'manage_reports', 'view_reports', 'create_reports', 'edit_reports', 'delete_reports',
            // User management permissions
            'manage_users', 'view_users', 'create_users', 'edit_users', 'delete_users',
            // Role management permissions
            'manage_roles', 'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
            // Permission management permissions
            'manage_permissions', 'view_permissions', 'create_permissions', 'edit_permissions', 'delete_permissions',
            // Platform management permissions
            'manage_platform',
        ];

        foreach ($permissionNames as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $platformAdmin = Role::create(['name' => 'platform_admin', 'guard_name' => 'web']);
        $platformAdmin->givePermissionTo($permissionNames);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('platform_admin');
    }
}
