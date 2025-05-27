<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberGroup extends Model
{
    protected $fillable = ['name'];

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'member_group_participant');
    }
}
