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

    public $translatable = ['name', 'description'];
}
