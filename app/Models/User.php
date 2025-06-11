<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'referral_code', 'discount_eligible', 'discount_used',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    public function getOrganizationAttribute()
    {
        return $this->setting ? $this->setting->organization_name : 'Org';
    }

    public function bonusCredits()
    {
        return $this->hasMany(BonusCredit::class, 'user_id');
    }
    
    public function activities()
    {
        return $this->hasMany(UserActivity::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(bin2hex(random_bytes(3)));
            }
        });
    }

    // Removed getPermissionsAttribute to rely on Spatie's default permissions relationship
}
