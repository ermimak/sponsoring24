<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supporter extends Model
{
    use HasFactory;

    protected $table = 'supporters';

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
        'email',
        'phone',
    ];

    /**
     * Get the donations associated with the supporter.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get the projects the supporter has donated to.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'donations');
    }

    /**
     * Get the participants the supporter has donated for.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'donations');
    }

    // Assuming a Payment model exists and is related
    /**
     * Get the payments made by the supporter.
     */
    // public function payments(): HasMany
    // {
    //     return $this->hasMany(Payment::class);
    // }
} 