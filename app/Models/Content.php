<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'section',
        'title',
        'subtitle',
        'content',
        'image',
        'is_active',
        'order',
        'meta'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid' => 'string',
        'content' => 'array',
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get content by section name.
     *
     * @param string $section
     * @return Content|null
     */
    public static function getBySection(string $section)
    {
        return self::where('section', $section)->first();
    }
    
    /**
     * Get active content by section name.
     *
     * @param string $section
     * @return Content|null
     */
    public static function getActiveBySectionName(string $section)
    {
        return self::where('section', $section)
            ->where('is_active', true)
            ->orderBy('order')
            ->first();
    }
    
    /**
     * Get all active content by section name.
     *
     * @param string $section
     * @return Collection
     */
    public static function getAllActiveBySection(string $section)
    {
        return self::where('section', $section)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }
}
