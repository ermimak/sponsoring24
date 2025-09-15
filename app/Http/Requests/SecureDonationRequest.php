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
        return [
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
            'donor_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s\-\.\']+$/u' // Only letters, spaces, hyphens, dots, apostrophes
            ],
            'donor_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'filter:FILTER_VALIDATE_EMAIL'
            ],
            'donor_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/' // Phone number format
            ],
            'donor_address' => [
                'nullable',
                'string',
                'max:255'
            ],
            'donor_city' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-\.\']+$/u'
            ],
            'donor_postal_code' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\s\-]+$/i'
            ],
            'donor_country' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-\.\']+$/u'
            ],
            'message' => [
                'nullable',
                'string',
                'max:1000' // Limit message length
            ],
            'anonymous' => [
                'boolean'
            ],
            'newsletter' => [
                'boolean'
            ],
            'payment_method' => [
                'required',
                'string',
                Rule::in(['stripe', 'invoice', 'bank_transfer'])
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.regex' => 'Amount can only have up to 2 decimal places.',
            'donor_name.regex' => 'Name can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'donor_phone.regex' => 'Please enter a valid phone number.',
            'donor_city.regex' => 'City can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'donor_postal_code.regex' => 'Please enter a valid postal code.',
            'donor_country.regex' => 'Country can only contain letters, spaces, hyphens, dots, and apostrophes.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize inputs
        $this->merge([
            'donor_name' => $this->sanitizeString($this->donor_name),
            'donor_email' => $this->sanitizeEmail($this->donor_email),
            'donor_address' => $this->sanitizeString($this->donor_address),
            'donor_city' => $this->sanitizeString($this->donor_city),
            'donor_country' => $this->sanitizeString($this->donor_country),
            'message' => $this->sanitizeString($this->message),
            'amount' => $this->sanitizeAmount($this->amount),
        ]);
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
