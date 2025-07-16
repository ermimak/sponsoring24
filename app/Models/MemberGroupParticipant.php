<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberGroupParticipant extends Pivot
{
    use HasUuid;

    protected $table = 'member_group_participant';

    protected $fillable = [
        'member_group_id',
        'participant_id',
    ];

    /**
     * Get the member group that owns the pivot.
     */
    public function memberGroup(): BelongsTo
    {
        return $this->belongsTo(MemberGroup::class);
    }

    /**
     * Get the participant that owns the pivot.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
