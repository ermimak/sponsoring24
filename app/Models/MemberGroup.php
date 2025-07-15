<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MemberGroup extends Model
{
    use HasUuid;
    protected $fillable = ['uuid', 'name'];
    
    protected $casts = [
        'uuid' => 'string',
    ];

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'member_group_participant');
    }
}
