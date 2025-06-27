<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'license_key',
        'status',
        'type',
        'issued_at',
        'expires_at',
        'payment_id',
        'amount',
        'currency',
        'discount_applied',
        'discount_amount',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'discount_applied' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the license.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the license is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    /**
     * Check if the license is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at <= now();
    }

    /**
     * Get the days remaining until expiration.
     *
     * @return int
     */
    public function daysRemaining()
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }
}
