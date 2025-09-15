<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class SecureFileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'image_landscape' => [
                'nullable',
                File::image()
                    ->max(5 * 1024) // 5MB max
                    ->dimensions(['min_width' => 100, 'min_height' => 100, 'max_width' => 4000, 'max_height' => 4000]),
            ],
            'image_portrait' => [
                'nullable',
                File::image()
                    ->max(5 * 1024) // 5MB max
                    ->dimensions(['min_width' => 100, 'min_height' => 100, 'max_width' => 4000, 'max_height' => 4000]),
            ],
            'file' => [
                'nullable',
                'file',
                'max:10240', // 10MB max
                'mimes:pdf,doc,docx,xls,xlsx,csv,txt',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'image_landscape.max' => 'Landscape image must not exceed 5MB.',
            'image_portrait.max' => 'Portrait image must not exceed 5MB.',
            'file.max' => 'File must not exceed 10MB.',
            'file.mimes' => 'File must be a PDF, Word document, Excel file, CSV, or text file.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional security checks
            foreach (['image_landscape', 'image_portrait', 'file'] as $field) {
                if ($this->hasFile($field)) {
                    $file = $this->file($field);
                    
                    // Check for executable files
                    if ($this->isExecutableFile($file)) {
                        $validator->errors()->add($field, 'Executable files are not allowed.');
                    }
                    
                    // Check file content for malicious patterns
                    if ($this->containsMaliciousContent($file)) {
                        $validator->errors()->add($field, 'File contains potentially malicious content.');
                    }
                    
                    // Verify MIME type matches extension
                    if (!$this->verifyMimeType($file)) {
                        $validator->errors()->add($field, 'File type does not match its content.');
                    }
                }
            }
        });
    }

    /**
     * Check if file is executable
     */
    private function isExecutableFile($file): bool
    {
        $dangerousExtensions = [
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar',
            'php', 'asp', 'aspx', 'jsp', 'py', 'pl', 'rb', 'sh', 'ps1'
        ];
        
        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, $dangerousExtensions);
    }

    /**
     * Check for malicious content in file
     */
    private function containsMaliciousContent($file): bool
    {
        // Only check text-based files
        $textMimes = ['text/plain', 'text/csv', 'application/csv'];
        if (!in_array($file->getMimeType(), $textMimes)) {
            return false;
        }

        $content = file_get_contents($file->getPathname());
        
        // Check for common malicious patterns
        $maliciousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/eval\s*\(/i',
            '/base64_decode/i',
            '/system\s*\(/i',
            '/exec\s*\(/i',
            '/shell_exec/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify MIME type matches file extension
     */
    private function verifyMimeType($file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        
        $allowedMimes = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'csv' => ['text/csv', 'application/csv', 'text/plain'],
            'txt' => ['text/plain'],
        ];

        if (!isset($allowedMimes[$extension])) {
            return false;
        }

        return in_array($mimeType, $allowedMimes[$extension]);
    }
}
