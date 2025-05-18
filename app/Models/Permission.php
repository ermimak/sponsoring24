<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends SpatiePermission
{
    use HasFactory, RefreshesPermissionCache;

    protected $fillable = [
        'name',
        'guard_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getDisplayNameAttribute()
    {
        return ucwords(str_replace('-', ' ', $this->name));
    }

    public function scopeGuard($query, $guard)
    {
        return $query->where('guard_name', $guard);
    }
}