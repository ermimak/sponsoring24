<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
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

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'participant_project')
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
