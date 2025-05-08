<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'project_id',
        'participant_id',
        'supporter_id',
        'amount',
        'type',
        'billing_date',
        'status',
        'payment_method',
        'currency',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
} 