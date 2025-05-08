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
        'email',
        'email_cc',
        'phone',
        'member_id',
        'archived',
    ];

    public function memberGroups()
    {
        return $this->belongsToMany(MemberGroup::class, 'member_group_participant');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'participant_project')
            ->withPivot(['status', 'role'])
            ->withTimestamps();
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
