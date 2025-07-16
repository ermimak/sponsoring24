<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDonationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $donation;
    protected $amount;
    protected $projectName;
    protected $donorName;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->donation = $data['donation'];
        $this->amount = $data['amount'];

        $project = $data['project'];
        $donor = $data['donor'] ?? null;

        // Set Project Name
        $this->projectName = $this->getProjectName($project);

        // Set Donor Name
        $this->donorName = $this->getDonorName($donor);
    }

    private function getProjectName($project): string
    {
        if (is_string($project)) {
            $projectData = json_decode($project, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $project = (object) $projectData;
            }
        }

        if (is_object($project) && property_exists($project, 'name')) {
            $name = $project->name;
            if (is_array($name) || is_object($name)) {
                $name = (array) $name;
                $locale = app()->getLocale();
                if (!empty($name[$locale])) {
                    return $name[$locale];
                }
                // Fallback to the first available language
                return reset($name) ?: 'Unnamed Project';
            }
            return (string) $name;
        }

        return 'Unnamed Project';
    }

    private function getDonorName($donor): string
    {
        if (is_string($donor)) {
            $donorData = json_decode($donor, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $donor = (object) $donorData;
            }
        }

        if (is_object($donor)) {
            if (!empty($donor->first_name) || !empty($donor->last_name)) {
                return trim(($donor->first_name ?? '') . ' ' . ($donor->last_name ?? ''));
            }
            if (!empty($donor->name)) {
                return $donor->name;
            }
            if (!empty($donor->email)) {
                return $donor->email;
            }
        }

        return $donor ? (string) $donor : 'Anonymous';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Donation Received')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new donation of CHF ' . number_format($this->amount, 2) . ' has been received for project "' . $this->projectName . '".')
            ->line('Donor: ' . $this->donorName)
            ->action('View Donation Details', url('/dashboard'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'donation_id' => $this->donation->id ?? null,
            'amount' => $this->amount,
            'project_name' => $this->projectName,
            'donor_name' => $this->donorName,
            'message' => 'New donation of CHF ' . number_format($this->amount, 2) . ' received from ' . $this->donorName . ' for project "' . $this->projectName . '"',
            'type' => 'new_donation',
        ];
    }
}
