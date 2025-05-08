<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ParticipantProject extends Pivot
{
    protected $table = 'participant_project';
    protected $fillable = [
        'participant_id',
        'project_id',
        'status',
        'role',
    ];
} 