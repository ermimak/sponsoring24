<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        
        // Create a super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@fundoo.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('fundoo@admin123'),
                'approval_status' => 'approved',
                'referral_code' => 'SUPERADMIN',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Assign the super-admin role
        $superAdmin->assignRole($superAdminRole);
        
        $this->command->info('Super Admin user created with email: admin@fundoo.com and password: fundoo@admin123');
        $this->command->info('User role created successfully');
    }
}
