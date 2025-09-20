<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SecureDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $step = $this->input('step', 'donation');
        
        $rules = [
            'step' => [
                'required',
                'string',
                Rule::in(['donation', 'confirmation'])
            ],
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:100000', // Reasonable maximum
                'regex:/^\d+(\.\d{1,2})?$/' // Only allow up to 2 decimal places
            ],
            'currency' => [
                'required',
                'string',
                Rule::in(['CHF', 'EUR', 'USD']) // Only allow supported currencies
            ],
        ];

        // Add validation rules based on step
        if ($step === 'confirmation') {
            $rules = array_merge($rules, [
                'gender' => [
                    'required',
                    'string',
                    Rule::in(['Masculine', 'Feminine', 'Other'])
                ],
                'first_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                    'regex:/^[\p{L}\s\-\.\']+$/u' // Only letters, spaces, hyphens, dots, apostrophes
                ],
                'last_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                    'regex:/^[\p{L}\s\-\.\']+$/u'
                ],
                'company' => [
                    'nullable',
                    'string',
                    'max:255'
                ],
                'address' => [
                    'required',
                    'string',
                    'max:255'
                ],
                'address_suffix' => [
                    'nullable',
                    'string',
                    'max:255'
                ],
                'postal_code' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^[A-Z0-9\s\-]+$/i'
                ],
                'location' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[\p{L}\s\-\.\']+$/u'
                ],
                'country' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[\p{L}\s\-\.\']+$/u'
                ],
                'email' => [
                    'required',
                    'email:rfc,dns',
                    'max:255'
                ],
                'phone' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^[\+]?[0-9\s\-\(\)]+$/' // Phone number format
                ],
                'privacy_policy' => [
                    'required',
                    'accepted'
                ],
            ]);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.regex' => 'Amount can only have up to 2 decimal places.',
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'phone.regex' => 'Please enter a valid phone number.',
            'location.regex' => 'Location can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'postal_code.regex' => 'Please enter a valid postal code.',
            'country.regex' => 'Country can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'privacy_policy.accepted' => 'You must accept the privacy policy to continue.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize inputs
        $sanitized = [
            'amount' => $this->sanitizeAmount($this->amount),
        ];

        // Only sanitize confirmation step fields if they exist
        if ($this->input('step') === 'confirmation') {
            $sanitized = array_merge($sanitized, [
                'first_name' => $this->sanitizeString($this->first_name),
                'last_name' => $this->sanitizeString($this->last_name),
                'company' => $this->sanitizeString($this->company),
                'email' => $this->sanitizeEmail($this->email),
                'address' => $this->sanitizeString($this->address),
                'address_suffix' => $this->sanitizeString($this->address_suffix),
                'location' => $this->sanitizeString($this->location),
                'country' => $this->sanitizeString($this->country),
            ]);
        }

        $this->merge($sanitized);
    }

    /**
     * Sanitize string input
     */
    private function sanitizeString(?string $input): ?string
    {
        if (!$input) return null;
        
        // Remove HTML tags and encode special characters
        $sanitized = strip_tags($input);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        
        return trim($sanitized);
    }

    /**
     * Sanitize email input
     */
    private function sanitizeEmail(?string $email): ?string
    {
        if (!$email) return null;
        
        return filter_var(trim(strtolower($email)), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize amount input
     */
    private function sanitizeAmount($amount)
    {
        if (!$amount) return null;
        
        // Remove any non-numeric characters except decimal point
        $sanitized = preg_replace('/[^0-9.]/', '', $amount);
        
        // Ensure only one decimal point
        $parts = explode('.', $sanitized);
        if (count($parts) > 2) {
            $sanitized = $parts[0] . '.' . $parts[1];
        }
        
        return $sanitized;
    }
}
