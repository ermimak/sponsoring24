<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticipantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gender' => 'nullable|in:male,female,other',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'company' => 'nullable|string',
            'address' => 'nullable|string',
            'address_suffix' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'location' => 'nullable|string',
            'country' => 'required|string',
            'birthday' => 'nullable|date',
            'email' => 'required|email|unique:participants,email',
            'email_cc' => 'nullable|email',
            'phone' => 'nullable|string',
            'member_id' => 'nullable|string',
            'archived' => 'boolean',
        ];
    }
}
