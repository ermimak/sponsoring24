<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',           // The referrer
        'referred_user_id',  // The user who was referred
        'amount',
        'status',            // e.g. 'pending', 'credited'
        'referral_code_used', // The referral code that was used
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
