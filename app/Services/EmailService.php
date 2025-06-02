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
                'first_name' => $participant->first_name,
                'last_name' => $participant->last_name,
                'project_name' => $project->name ?? '',
                'project_description' => $project->description ?? '',
            ], $additionalData);

            $subject = $this->replacePlaceholders($subject, [
                'first_name' => $participant->first_name,
                'last_name' => $participant->last_name,
                'project_name' => $project->name ?? '',
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
        if (!$donation->donor_email) {
            Log::warning('Cannot send email to donation without donor email', [
                'donation_id' => $donation->id,
                'template_id' => $template->id,
            ]);
            return false;
        }

        try {
            $project = $template->project;
            $participant = $donation->participant;
            
            $body = $this->replacePlaceholders($body, [
                'donor_name' => $donation->donor_name,
                'donor_email' => $donation->donor_email,
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'participant_name' => $participant ? $participant->first_name . ' ' . $participant->last_name : '',
                'project_name' => $project->name ?? '',
                'project_description' => $project->description ?? '',
            ], $additionalData);

            $subject = $this->replacePlaceholders($subject, [
                'donor_name' => $donation->donor_name,
                'project_name' => $project->name ?? '',
                'amount' => $donation->amount,
                'currency' => $donation->currency,
            ], $additionalData);

            // Send email using Laravel Mail
            $htmlContent = $this->buildEmailHtml($body, $template);
            $to = [$donation->donor_email => $donation->donor_name];
            $replyTo = $template->reply_to ? [$template->reply_to => $template->sender_name ?? config('mail.from.name')] : [];
            
            if ($template->regarding) {
                $subject = $template->regarding . ': ' . $subject;
            }
            
            Mail::html($htmlContent, function($message) use ($to, $subject, $replyTo) {
                $message->to(key($to), current($to));
                $message->subject($subject);
                
                if (!empty($replyTo)) {
                    $message->replyTo(key($replyTo), current($replyTo));
                }
            });
            
            Log::info('Email sent to donation supporter', [
                'donation_id' => $donation->id,
                'donor_email' => $donation->donor_email,
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
        $data = array_merge($standardData, $additionalData);
        
        foreach ($data as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
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
                }
                .footer {
                    margin-top: 20px;
                    font-size: 12px;
                    color: #777;
                    text-align: center;
                }
                .logo {
                    max-width: 150px;
                    margin-bottom: 15px;
                }
                .header-image {
                    width: 100%;
                    max-height: 200px;
                    object-fit: cover;
                    margin-bottom: 15px;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">';
        
        if ($template->show_logo) {
            $html .= '<img src="' . config('app.url') . '/images/logo.png" alt="Logo" class="logo">';
        }
        
        if ($template->show_header_image && $template->project && $template->project->image_landscape) {
            $html .= '<img src="' . $template->project->image_landscape . '" alt="Project Header" class="header-image">';
        }
        
        $html .= '</div>
                <div class="content">
                    ' . $body . '
                </div>
                <div class="footer">';
        
        if ($template->footer) {
            $html .= $template->footer;
        } else {
            $html .= '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.';
        }
        
        if ($template->show_placeholders) {
            $html .= '<div style="margin-top: 15px; padding: 10px; background-color: #f0f0f0; border: 1px dashed #ccc;">
                <p style="font-size: 11px; color: #666;">Available placeholders: {{first_name}}, {{last_name}}, {{email}}, {{project_name}}, {{project_description}}, {{donor_name}}, {{donor_email}}, {{amount}}, {{currency}}, {{participant_name}}</p>
            </div>';
        }
        
        $html .= '</div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
