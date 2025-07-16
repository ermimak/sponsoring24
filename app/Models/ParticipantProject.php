<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ParticipantProject extends Pivot
{
    use HasUuid;
    protected $table = 'participant_project';

    protected $fillable = [
        'participant_id',
        'project_id',
        'status',
        'role',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Method to mark the landing page as opened
    public function markLandingPageOpened()
    {
        $this->participant()->update(['landing_page_opened' => true]);
    }
}
