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
        'currency',
        'type',
        'billing_date',
        'status',
        'payment_method',
        'supporter_email',
    ];

    protected $casts = [
        'billing_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function supporter()
    {
        return $this->belongsTo(Supporter::class);
    }
}