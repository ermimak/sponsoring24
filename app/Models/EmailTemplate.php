<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'type',
        'name',
        'subject',
        'body',
        'footer',
        'notes',
        'show_logo',
        'show_header_image',
        'show_placeholders',
        'regarding',
        'reply_to',
        'sender_name',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
