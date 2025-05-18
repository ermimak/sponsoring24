<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
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

    // Removed getPermissionsAttribute to rely on Spatie's default permissions relationship
}