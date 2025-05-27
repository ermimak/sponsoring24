<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_suffix' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'email_cc' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'member_id' => ['nullable', 'string', 'max:255'],
            'public_registration' => ['nullable', 'boolean'],
            'archived' => ['nullable', 'boolean'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['string', 'max:255'],
        ];
    }
}
