<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'location' => $this->location,
            'language' => $this->language,
            'start' => $this->start,
            'end' => $this->end,
            'allow_donation_until' => $this->allow_donation_until,
            'image_landscape' => $this->image_landscape,
            'image_square' => $this->image_square,
            'flat_rate_enabled' => $this->flat_rate_enabled,
            'flat_rate_min_amount' => $this->flat_rate_min_amount,
            'flat_rate_help_text' => $this->flat_rate_help_text,
            'unit_based_enabled' => $this->unit_based_enabled,
            'public_donation_enabled' => $this->public_donation_enabled,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
