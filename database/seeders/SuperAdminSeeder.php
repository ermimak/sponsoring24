<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        
        // Create a super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@sponsoring24.app'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('sponsoring24@admin123'),
                'approval_status' => 'approved',
                'referral_code' => 'SUPERADMIN',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Assign the super-admin role
        $superAdmin->assignRole($superAdminRole);
        
        $this->command->info('Super Admin user created with email: admin@sponsoring24.app and password: sponsoring24@admin123');
        $this->command->info('User role created successfully');
    }
}
