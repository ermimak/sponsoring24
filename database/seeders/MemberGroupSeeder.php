<?php

namespace Database\Seeders;

use App\Models\MemberGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class MemberGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Seeding member groups');

        $groups = [
            ['name' => 'VIP Members'],
            ['name' => 'Regular Members'],
            ['name' => 'Corporate Sponsors'],
            ['name' => 'Community Partners'],
            ['name' => 'Event Volunteers'],
        ];

        foreach ($groups as $group) {
            MemberGroup::updateOrCreate(
                ['name' => $group['name']],
                $group
            );
        }

        Log::info('Member groups seeded successfully: ' . count($groups) . ' groups');
    }
}
