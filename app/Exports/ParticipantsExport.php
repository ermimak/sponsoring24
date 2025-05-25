<?php

namespace App\Exports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParticipantsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Participant::with('memberGroups')->get()->map(function ($participant) {
            return [
                'id' => $participant->id,
                'gender' => $participant->gender,
                'first_name' => $participant->first_name,
                'last_name' => $participant->last_name,
                'company' => $participant->company,
                'address' => $participant->address,
                'address_suffix' => $participant->address_suffix,
                'postal_code' => $participant->postal_code,
                'location' => $participant->location,
                'country' => $participant->country,
                'birthday' => $participant->birthday,
                'email' => $participant->email,
                'email_cc' => $participant->email_cc,
                'phone' => $participant->phone,
                'member_id' => $participant->member_id,
                'groups' => $participant->memberGroups->pluck('name')->join(', '),
                'archived' => $participant->archived ? 'Yes' : 'No',
                'created_at' => $participant->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Gender',
            'First Name',
            'Last Name',
            'Company',
            'Address',
            'Address Suffix',
            'Postal Code',
            'Location',
            'Country',
            'Birthday',
            'Email',
            'Email CC',
            'Phone',
            'Member ID',
            'Groups',
            'Archived',
            'Created At',
        ];
    }
}
