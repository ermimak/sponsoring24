<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MemberGroup extends Model
{
    use HasUuid;
    protected $fillable = ['name'];

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'member_group_participant', 'member_group_id', 'participant_id')
            ->using(MemberGroupParticipant::class)
            ->withTimestamps();
    }
}
