<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectParticipantsExport implements FromCollection, WithHeadings
{
    protected $participants;
    protected $projectId;

    public function __construct($participants, $projectId = null)
    {
        $this->participants = $participants;
        $this->projectId = $projectId;
    }

    public function collection()
    {
        return $this->participants->map(function ($participant) {
            $projectId = $this->projectId;
            
            return [
                'id' => $participant->id,
                'first_name' => $participant->first_name,
                'last_name' => $participant->last_name,
                'email' => $participant->email,
                'groups' => $participant->memberGroups->pluck('name')->join(', '),
                'supporters' => $projectId ? $participant->getSupportersForProject($projectId) : $participant->supporters,
                'sales_volume' => $projectId ? $participant->getSalesVolumeForProject($projectId) : $participant->sales_volume,
                'emails_sent' => $projectId ? $participant->getEmailsSentForProject($projectId) : $participant->emails_sent,
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
