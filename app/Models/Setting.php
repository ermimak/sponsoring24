<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_name',
        'contact_title',
        'contact_first_name',
        'contact_last_name',
        'address',
        'address_suffix',
        'postal_code',
        'location',
        'country',
        'language',
        'email',
        'phone',
        'accent_color',
        'logo_path',
        'billing_salutation',
        'billing_first_name',
        'billing_last_name',
        'billing_address',
        'billing_address_suffix',
        'billing_postal_code',
        'billing_location',
        'billing_country',
        'billing_email',
        'billing_phone',
        'bank_account_details',
        'iban',
        'recipient',
        'project_overview_enabled',
        'user_id',
        'organization_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'project_overview_enabled' => 'boolean',
        ];
    }

    protected $attributes = [
        'accent_color' => '#9500FF',
        'country' => 'Switzerland',
        'language' => 'German',
        'billing_country' => 'Switzerland',
        'billing_last_name' => '',
        'billing_address_suffix' => '',
    ];

    /**
     * Get the user who manages these settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization associated with these settings.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the projects affected by these settings.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}