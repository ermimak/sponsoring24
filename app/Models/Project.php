<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUuid;

    protected $fillable = [
        'id',
        'name',
        'description',
        'location',
        'language',
        'start',
        'end',
        'allow_donation_until',
        'image_landscape',
        'image_square',
        'flat_rate_enabled',
        'flat_rate_min_amount',
        'flat_rate_help_text',
        'unit_based_enabled',
        'public_donation_enabled',
        'created_by',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'flat_rate_enabled' => 'boolean',
        'unit_based_enabled' => 'boolean',
        'public_donation_enabled' => 'boolean',
        'start' => 'datetime',
        'end' => 'datetime',
        'allow_donation_until' => 'datetime',
    ];

    public $translatable = ['name', 'description'];

    // UUID handling is now managed by the HasUuid trait

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'participant_project', 'project_id', 'participant_id')
            ->using(ParticipantProject::class)
            ->withPivot(['status', 'role'])
            ->withTimestamps();
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class);
    }
    
}
