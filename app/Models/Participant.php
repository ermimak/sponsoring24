<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'gender',
        'first_name',
        'last_name',
        'company',
        'address',
        'address_suffix',
        'postal_code',
        'location',
        'country',
        'birthday',
        'member_id',
        'email',
        'email_cc',
        'phone',
        'public_registration',
        'archived',
        'email_status',
        'landing_page_opened',
    ];

    protected $casts = [
        'public_registration' => 'boolean',
        'archived' => 'boolean',
        'birthday' => 'date',
        'landing_page_opened' => 'boolean',
    ];

    public function memberGroups()
    {
        return $this->belongsToMany(MemberGroup::class, 'member_group_participant', 'participant_id', 'member_group_id')
            ->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'participant_project', 'participant_id', 'project_id')
            ->withPivot(['status', 'role'])
            ->withTimestamps();
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function getSupportersAttribute()
    {
        return $this->donations()->distinct('supporter_id')->count('supporter_id');
    }

    public function getSalesVolumeAttribute()
    {
        return $this->donations()->sum('amount') ?? 0;
    }

    public function getEmailsSentAttribute()
    {
        return 0; // Placeholder
    }
}
