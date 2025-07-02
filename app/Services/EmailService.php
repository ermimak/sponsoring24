<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\EmailTemplate;
use App\Models\Participant;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send an email to a participant
     *
     * @param Participant $participant
     * @param EmailTemplate $template
     * @param string $subject
     * @param string $body
     * @param array $additionalData
     * @return bool
     */
    public function sendToParticipant(Participant $participant, EmailTemplate $template, string $subject, string $body, array $additionalData = [])
    {
        try {
            // Check if participant has an email address
            if (empty($participant->email)) {
                Log::warning('Cannot send email to participant without email address', [
                    'participant_id' => $participant->id,
                ]);
                return false;
            }

            // Get project from template or participant's first project
            $project = $template->project ?? $participant->projects->first();
            
            Log::info('Preparing email for participant', [
                'participant_id' => $participant->id,
                'email' => $participant->email,
                'template_id' => $template->id,
            ]);
            
            $body = $this->replacePlaceholders($body, [
                // Participant data
                'first_name' => $participant->first_name,
                'last_name' => $participant->last_name,
                'email' => $participant->email,
                'member_id' => $participant->member_id,
                'company' => $participant->company,
                'address' => $participant->address,
                'postal_code' => $participant->postal_code,
                'location' => $participant->location,
                'country' => $participant->country,
                'phone' => $participant->phone,
                
                // Project data
                'project_name' => $project->name ?? '',
                'project_description' => $project->description ?? '',
                'project_start_date' => $project->start_date ?? '',
                'project_end_date' => $project->end_date ?? '',
                
                // System defaults
                'currency' => $project->currency ?? 'CHF',
                'date' => now()->format('d.m.Y'),
            ], $additionalData);

            $subject = $this->replacePlaceholders($subject, [
                // Participant data
                'first_name' => $participant->first_name,
                'last_name' => $participant->last_name,
                'email' => $participant->email,
                'member_id' => $participant->member_id,
                
                // Project data
                'project_name' => $project->name ?? '',
                
                // System defaults
                'currency' => $project->currency ?? 'CHF',
                'date' => now()->format('d.m.Y'),
            ], $additionalData);

            // Send email using Laravel Mail
            $htmlContent = $this->buildEmailHtml($body, $template);
            $to = $participant->email;
            $replyTo = $template->reply_to ? [$template->reply_to => $template->sender_name ?? config('mail.from.name')] : [];
            
            if ($template->regarding) {
                $subject = $template->regarding . ': ' . $subject;
            }
            
            try {
                Mail::html($htmlContent, function($message) use ($to, $subject, $replyTo) {
                    $message->to($to);
                    $message->subject($subject);
                    
                    if (!empty($replyTo)) {
                        $message->replyTo(key($replyTo), current($replyTo));
                    }
                });
                
                // Update participant's email count
                $participant->increment('emails_sent', 1);
                
                Log::info('Email sent to participant successfully', [
                    'participant_id' => $participant->id,
                    'email' => $participant->email,
                    'template_id' => $template->id,
                    'template_type' => $template->type,
                ]);
                
                return true;
            } catch (\Exception $e) {
                Log::error('Mail delivery failed', [
                    'error' => $e->getMessage(),
                    'participant_id' => $participant->id,
                    'email' => $participant->email,
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email to participant', [
                'participant_id' => $participant->id,
                'email' => $participant->email ?? 'unknown',
                'template_id' => $template->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return false;
        }
    }

    /**
     * Send an email to a donation's supporter
     *
     * @param Donation $donation
     * @param EmailTemplate $template
     * @param string $subject
     * @param string $body
     * @param array $additionalData
     * @return bool
     */
    public function sendToDonation(Donation $donation, EmailTemplate $template, string $subject, string $body, array $additionalData = [])
    {
        // Check if we're in test mode (indicated by specific additionalData flag)
        $isTestMode = isset($additionalData['is_test_mode']) && $additionalData['is_test_mode'] === true;
        
        // If not in test mode and donor email is missing, we can't send the email
        if (!$donation->donor_email && !$isTestMode) {
            Log::warning('Cannot send email to donation without donor email', [
                'donation_id' => $donation->id,
                'template_id' => $template->id,
            ]);
            return false;
        }
        
        // For test mode, use a default test email if donor_email is missing
        $recipientEmail = $donation->donor_email;
        if (!$recipientEmail && $isTestMode) {
            $recipientEmail = 'test@example.com';
            Log::info('Using test email for donation in test mode', [
                'donation_id' => $donation->id,
                'test_email' => $recipientEmail
            ]);
        }

        try {
            $project = $template->project;
            $participant = $donation->participant;
            
            // Replace placeholders in subject first
            $subject = $this->replacePlaceholders($subject, [
                'donor_name' => $donation->donor_name ?? 'Donor',
                'project_name' => $project ? $project->name : config('app.name', 'Sponsoring24'),
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'participant_name' => $participant ? $participant->first_name . ' ' . $participant->last_name : 'Participant',
            ], $additionalData);
            
            $body = $this->replacePlaceholders($body, [
                // Donation data
                'donor_name' => $donation->donor_name,
                'donor_email' => $donation->donor_email,
                'donor_address' => $donation->donor_address,
                'donor_postal_code' => $donation->donor_postal_code,
                'donor_location' => $donation->donor_location,
                'donor_country' => $donation->donor_country,
                'donor_message' => $donation->message,
                'amount' => $donation->amount,
                'donation_currency' => $donation->currency,
                'currency' => $donation->currency,
                'donation_date' => $donation->created_at->format('d.m.Y'),
                'donation_id' => $donation->id,
                'donation_status' => $donation->status,
                
                // Participant data
                'participant_name' => $participant ? $participant->first_name . ' ' . $participant->last_name : '',
                'participant_first_name' => $participant ? $participant->first_name : '',
                'participant_last_name' => $participant ? $participant->last_name : '',
                'participant_email' => $participant ? $participant->email : '',
                'participant_id' => $participant ? $participant->member_id : '',
                'first_name' => $participant ? $participant->first_name : '',
                'last_name' => $participant ? $participant->last_name : '',
                
                // Project data
                'project_name' => $project->name ?? '',
                'project_description' => $project->description ?? '',
                'project_start_date' => $project->start_date ?? '',
                'project_end_date' => $project->end_date ?? '',
                
                // System defaults
                'date' => now()->format('d.m.Y'),
            ], $additionalData);

            // Build the HTML email
            $htmlContent = $this->buildEmailHtml($body, $template);
            
            // Prepare recipient information
            $to = [$recipientEmail => $donation->donor_name ?? 'Donor'];
            $replyTo = $template->reply_to ? [$template->reply_to => $template->sender_name ?? config('mail.from.name')] : [];
            
            // Add regarding prefix to subject if available
            if ($template->regarding) {
                $subject = $template->regarding . ': ' . $subject;
            }
            
            // Send the email
            Mail::html($htmlContent, function($message) use ($to, $subject, $replyTo) {
                $message->to(key($to), current($to));
                $message->subject($subject);
                
                if (!empty($replyTo)) {
                    $message->replyTo(key($replyTo), current($replyTo));
                }
            });
            
            Log::info('Email sent to donation supporter', [
                'donation_id' => $donation->id,
                'donor_email' => $recipientEmail,
                'template_id' => $template->id,
                'template_type' => $template->type,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email to donation supporter', [
                'donation_id' => $donation->id,
                'donor_email' => $donation->donor_email,
                'template_id' => $template->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send mass emails to all participants in a project
     *
     * @param Project $project
     * @param EmailTemplate $template
     * @param string $subject
     * @param string $body
     * @param array $additionalData
     * @return array
     */
    public function sendMassEmailToProjectParticipants(Project $project, EmailTemplate $template, string $subject, string $body, array $additionalData = [])
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        Log::info('Starting mass email to project participants', [
            'project_id' => $project->id,
            'template_id' => $template->id,
        ]);

        $participants = Participant::whereHas('projects', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->get();

        Log::info('Found participants for mass email', [
            'count' => $participants->count(),
            'project_id' => $project->id,
        ]);

        foreach ($participants as $participant) {
            if (!$participant->email) {
                Log::info('Skipping participant without email', [
                    'participant_id' => $participant->id,
                ]);
                $results['skipped']++;
                continue;
            }

            try {
                Log::info('Attempting to send email to participant', [
                    'participant_id' => $participant->id,
                    'email' => $participant->email,
                ]);
                
                $success = $this->sendToParticipant($participant, $template, $subject, $body, $additionalData);
                
                if ($success) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    Log::error('Failed to send email to participant in mass email', [
                        'participant_id' => $participant->id,
                        'email' => $participant->email,
                    ]);
                }
            } catch (\Exception $e) {
                $results['failed']++;
                Log::error('Exception when sending email to participant in mass email', [
                    'participant_id' => $participant->id,
                    'email' => $participant->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Send mass emails to all donors in a project
     *
     * @param Project $project
     * @param EmailTemplate $template
     * @param string $subject
     * @param string $body
     * @param array $additionalData
     * @return array
     */
    public function sendMassEmailToProjectDonors(Project $project, EmailTemplate $template, string $subject, string $body, array $additionalData = [])
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        $donations = Donation::whereHas('participant.projects', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->get();

        foreach ($donations as $donation) {
            if (!$donation->donor_email) {
                $results['skipped']++;
                continue;
            }

            $success = $this->sendToDonation($donation, $template, $subject, $body, $additionalData);
            
            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Replace placeholders in a string with actual values
     *
     * @param string $text
     * @param array $standardData
     * @param array $additionalData
     * @return string
     */
    private function replacePlaceholders(string $text, array $standardData = [], array $additionalData = [])
    {
        // Merge standard data with additional data (additional data takes precedence)
        $data = array_merge($standardData, $additionalData);
        
        // Map legacy placeholder names to current data keys
        $legacyMappings = [
            'participant_first_name' => 'first_name',
            'participant_last_name' => 'last_name',
            'participant_email' => 'email',
            'participant_id' => 'member_id',
            'participant_company' => 'company',
            'participant_address' => 'address',
            'participant_postal_code' => 'postal_code',
            'participant_location' => 'location',
            'participant_country' => 'country',
            'participant_phone' => 'phone',
        ];
        
        // Apply legacy mappings if the target doesn't exist but the source does
        foreach ($legacyMappings as $legacy => $current) {
            if (!isset($data[$legacy]) && isset($data[$current])) {
                $data[$legacy] = $data[$current];
            }
        }
        
        // Create participant_name if first_name and last_name exist but participant_name doesn't
        if (!isset($data['participant_name']) && isset($data['first_name'])) {
            $data['participant_name'] = $data['first_name'];
            if (isset($data['last_name'])) {
                $data['participant_name'] .= ' ' . $data['last_name'];
            }
        }
        
        // Ensure we have default values for common fields if not provided
        $defaults = [
            'currency' => 'CHF',
            'date' => date('d.m.Y'),
            'project_name' => config('app.name', 'Sponsoring24'),
            'first_name' => 'Participant',
            'last_name' => '',
            'donor_name' => 'Donor',
            'amount' => '0.00',
            'company' => '',
            'address' => '',
            'location' => '',
            'postal_code' => ''
        ];
        
        foreach ($defaults as $key => $defaultValue) {
            if (!isset($data[$key]) || $data[$key] === null || $data[$key] === '') {
                $data[$key] = $defaultValue;
            }
        }
        
        // Handle amount formatting if amount exists
        if (isset($data['amount']) && is_numeric($data['amount'])) {
            $data['formatted_amount'] = number_format((float)$data['amount'], 2, '.', '\'');
        }
        
        // Replace all placeholders in the text
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $text = str_replace('{{' . $key . '}}', $value, $text);
            }
        }
        
        // Log any remaining placeholders for debugging
        if (preg_match_all('/\{\{([^}]+)\}\}/', $text, $matches)) {
            Log::warning('Unresolved email placeholders found', [
                'placeholders' => $matches[1],
                'available_data' => array_keys($data)
            ]);
        }
        
        return $text;
    }

    /**
     * Build HTML email with proper styling
     *
     * @param string $body
     * @param EmailTemplate $template
     * @return string
     */
    private function buildEmailHtml(string $body, EmailTemplate $template)
    {
        // Set default values for null fields
        $footer = $template->footer ?? '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.';
        $senderName = $template->sender_name ?? config('mail.from.name', 'Sponsoring24');
        $replyTo = $template->reply_to ?? config('mail.from.address', 'noreply@sponsoring24.app');
        $regarding = $template->regarding ?? '';
        $showLogo = $template->show_logo ?? false;
        $showHeaderImage = $template->show_header_image ?? false;
        $showPlaceholders = $template->show_placeholders ?? false;
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .content {
                    background-color: #f9f9f9;
                    padding: 20px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                    padding-top: 10px;
                    border-top: 1px solid #eee;
                    margin-top: 20px;
                }
                .regarding {
                    font-style: italic;
                    margin-bottom: 15px;
                    color: #555;
                }
                .contact {
                    margin-top: 15px;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">';
        
        if ($showLogo) {
            $html .= '<img src="' . config('app.url') . '/images/logo.png" alt="Logo" style="max-width: 200px;">';
        }
        
        if ($showHeaderImage) {
            $projectImage = $template->project && $template->project->image_landscape ? $template->project->image_landscape : config('app.url') . '/images/header.jpg';
            $html .= '<img src="' . $projectImage . '" alt="Header" style="max-width: 100%; margin-top: 20px;">';
        }
        
        $html .= '</div>';
        
        if (!empty($regarding)) {
            $html .= '<div class="regarding">Regarding: ' . htmlspecialchars($regarding) . '</div>';
        }
        
        $html .= '<div class="content">
                    ' . nl2br($body) . '
                </div>
                <div class="footer">';
        
        $html .= nl2br($footer);
        
        $html .= '<div class="contact">';
        if (!empty($senderName)) {
            $html .= htmlspecialchars($senderName) . '<br>';
        }
        if (!empty($replyTo)) {
            $html .= htmlspecialchars($replyTo);
        }
        $html .= '</div>';
        
        if ($showPlaceholders) {
            $html .= '<div style="margin-top: 15px; padding: 10px; background-color: #f0f0f0; border: 1px dashed #ccc;">
                <p style="font-size: 11px; color: #666;">Available placeholders: {{first_name}}, {{last_name}}, {{email}}, {{project_name}}, {{project_description}}, {{donor_name}}, {{donor_email}}, {{amount}}, {{currency}}, {{participant_name}}, {{participant_first_name}}, {{participant_last_name}}, {{date}}, {{landing_page_url}}, {{participant_donation_total}}</p>
            </div>';
        }
        
        $html .= '</div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
