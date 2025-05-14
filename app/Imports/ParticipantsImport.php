<?php

namespace App\Imports;

use App\Models\Participant;
use App\Models\MemberGroup;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $participant = Participant::create([
            'gender' => $row['gender'] ?? null,
            'first_name' => $row['first_name'] ?? null,
            'last_name' => $row['last_name'] ?? null,
            'company' => $row['company'] ?? null,
            'address' => $row['address'] ?? null,
            'address_suffix' => $row['address_suffix'] ?? null,
            'postal_code' => $row['postal_code'] ?? null,
            'location' => $row['location'] ?? null,
            'country' => $row['country'] ?? null,
            'birthday' => $row['birthday'] ?? null,
            'email' => $row['email'] ?? null,
            'email_cc' => $row['email_cc'] ?? null,
            'phone' => $row['phone'] ?? null,
            'member_id' => $row['member_id'] ?? null,
            'archived' => isset($row['archived']) && in_array(strtolower($row['archived']), ['yes', 'true', '1']),
        ]);

        if (!empty($row['groups'])) {
            $groupNames = array_map('trim', explode(',', $row['groups']));
            $groupIds = MemberGroup::whereIn('name', $groupNames)->pluck('id');
            $participant->memberGroups()->sync($groupIds);
        }

        return $participant;
    }
}