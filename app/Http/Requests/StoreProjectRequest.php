<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'name' => 'required|array',
            'name.de' => 'required|string',
            'description' => 'nullable|array',
            'location' => 'nullable|string',
            'language' => 'required|string|in:de,fr',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'allow_donation_until' => 'nullable|date|after_or_equal:start',
            'image_landscape' => 'nullable|string',
            'image_square' => 'nullable|string',
            'flat_rate_enabled' => 'boolean',
            'flat_rate_min_amount' => 'nullable|numeric|min:0',
            'flat_rate_help_text' => 'nullable|string',
            'unit_based_enabled' => 'boolean',
            'public_donation_enabled' => 'boolean',
        ];
    }
}
