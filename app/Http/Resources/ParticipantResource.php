<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
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
            'gender' => $this->gender,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'address' => $this->address,
            'address_suffix' => $this->address_suffix,
            'postal_code' => $this->postal_code,
            'location' => $this->location,
            'country' => $this->country,
            'birthday' => $this->birthday,
            'email' => $this->email,
            'email_cc' => $this->email_cc,
            'phone' => $this->phone,
            'member_id' => $this->member_id,
            'archived' => $this->archived,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
