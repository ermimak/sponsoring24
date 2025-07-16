<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supporter extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'supporters';

    // UUID handling is now managed by the HasUuid trait

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

    // Boot method is now handled by the HasUuid trait

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
