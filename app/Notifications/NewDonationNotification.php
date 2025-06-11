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
    public function __construct($donation, float $amount, string $projectName, string $donorName = 'Anonymous')
    {
        $this->donation = $donation;
        $this->amount = $amount;
        $this->projectName = $projectName;
        $this->donorName = $donorName;
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
