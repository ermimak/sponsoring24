<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Role;
use App\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Project::factory(10)->create();

        // Seed roles
        $platformAdmin = Role::create(['name' => 'platform_admin', 'label' => 'Platform Admin']);
        $orgAdmin = Role::create(['name' => 'org_admin', 'label' => 'Org Admin']);
        $donor = Role::create(['name' => 'donor', 'label' => 'Donor/Participant']);

        // Seed permissions
        $manageUsers = Permission::create(['name' => 'manage_users', 'label' => 'Manage Users']);
        $manageProjects = Permission::create(['name' => 'manage_projects', 'label' => 'Manage Projects']);
        $viewReports = Permission::create(['name' => 'view_reports', 'label' => 'View Reports']);
        $donate = Permission::create(['name' => 'donate', 'label' => 'Donate']);

        // Assign permissions to roles
        $platformAdmin->permissions()->attach([
            $manageUsers->id,
            $manageProjects->id,
            $viewReports->id,
            $donate->id,
        ]);
        $orgAdmin->permissions()->attach([
            $manageProjects->id,
            $viewReports->id,
            $donate->id,
        ]);
        $donor->permissions()->attach([
            $donate->id,
        ]);

        // Assign Platform Admin role to test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->roles()->attach($platformAdmin->id);
    }
}
