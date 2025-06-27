<?php

namespace Database\Seeders;

use App\Models\MemberGroup;
use App\Models\Participant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Seeding participants (members)');

        // Get the member groups to assign to participants
        $groups = MemberGroup::all();
        
        if ($groups->isEmpty()) {
            $this->command->info('No member groups found. Please run MemberGroupSeeder first.');
            return;
        }

        $participants = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'member_id' => 'JD001',
                'phone' => '+41 76 123 45 67',
                'gender' => 'male',
                'company' => 'ABC Corporation',
                'address' => 'Bahnhofstrasse 1',
                'postal_code' => '8001',
                'location' => 'Zürich',
                'country' => 'Switzerland',
                'groups' => ['VIP Members', 'Corporate Sponsors']
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'member_id' => 'JS002',
                'phone' => '+41 76 234 56 78',
                'gender' => 'female',
                'company' => 'XYZ Ltd',
                'address' => 'Seestrasse 10',
                'postal_code' => '6300',
                'location' => 'Zug',
                'country' => 'Switzerland',
                'groups' => ['Regular Members']
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@example.com',
                'member_id' => 'MJ003',
                'phone' => '+41 76 345 67 89',
                'gender' => 'male',
                'company' => 'Johnson & Co',
                'address' => 'Hauptstrasse 5',
                'postal_code' => '3000',
                'location' => 'Bern',
                'country' => 'Switzerland',
                'groups' => ['Corporate Sponsors', 'Community Partners']
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Williams',
                'email' => 'emma.williams@example.com',
                'member_id' => 'EW004',
                'phone' => '+41 76 456 78 90',
                'gender' => 'female',
                'company' => null,
                'address' => 'Kirchweg 8',
                'postal_code' => '4000',
                'location' => 'Basel',
                'country' => 'Switzerland',
                'groups' => ['Event Volunteers']
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Brown',
                'email' => 'robert.brown@example.com',
                'member_id' => 'RB005',
                'phone' => '+41 76 567 89 01',
                'gender' => 'male',
                'company' => 'Brown Enterprises',
                'address' => 'Seeweg 12',
                'postal_code' => '9000',
                'location' => 'St. Gallen',
                'country' => 'Switzerland',
                'groups' => ['VIP Members', 'Corporate Sponsors']
            ]
        ];

        foreach ($participants as $participantData) {
            $groupNames = $participantData['groups'] ?? [];
            unset($participantData['groups']);

            // Create or update the participant
            $participant = Participant::updateOrCreate(
                ['email' => $participantData['email']],
                $participantData
            );

            // Sync the member groups
            $groupIds = $groups->whereIn('name', $groupNames)->pluck('id')->toArray();
            $participant->memberGroups()->sync($groupIds);

            Log::info("Seeded participant: {$participant->first_name} {$participant->last_name} with groups: " . implode(', ', $groupNames));
        }

        Log::info('Participants seeded successfully: ' . count($participants) . ' participants');
    }
}
