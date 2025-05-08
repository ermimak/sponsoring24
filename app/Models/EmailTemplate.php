<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'name',
        'subject',
        'body',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
} 