<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasUuid;
    protected $fillable = [
        'uuid',
        'project_id',
        'participant_id',
        'supporter_id',
        'amount',
        'currency',
        'type',
        'billing_date',
        'status',
        'payment_method',
        'payment_id',
        'paid_at',
        'supporter_email',
        'confirmation_token',
        'confirmed_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'billing_date' => 'datetime',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
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
