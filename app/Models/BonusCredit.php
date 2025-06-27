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
        'credited',          // Boolean flag indicating if the bonus has been credited
        'payment_id',        // The payment ID associated with the bonus credit
        'credited_at',       // Timestamp when the bonus was credited
        'type',              // Type of bonus credit (e.g., 'referral', 'project')
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
