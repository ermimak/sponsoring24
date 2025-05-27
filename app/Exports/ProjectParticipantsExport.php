<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectParticipantsExport implements FromCollection, WithHeadings
{
    protected $participants;

    public function __construct($participants)
    {
        $this->participants = $participants;
    }

    public function collection()
    {
        return $this->participants->map(function ($participant) {
            return [
                'id' => $participant->id,
                'first_name' => $participant->first_name,
                'last_name' => $participant->last_name,
                'email' => $participant->email,
                'groups' => $participant->memberGroups->pluck('name')->join(', '),
                'supporters' => $participant->supporters,
                'sales_volume' => $participant->sales_volume,
                'emails_sent' => $participant->emails_sent,
                'landing_page_opened' => $participant->landing_page_opened ? 'Yes' : 'No',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Groups',
            'Supporters',
            'Sales Volume',
            'Emails Sent',
            'Landing Page Opened',
        ];
    }
}
