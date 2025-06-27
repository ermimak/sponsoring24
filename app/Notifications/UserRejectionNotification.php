<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRejectionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The rejection reason.
     *
     * @var string
     */
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Registration Status')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We regret to inform you that your account registration has not been approved.')
            ->line('Reason:')
            ->line($this->reason)
            ->line('If you believe this is an error or would like to provide additional information, please contact our support team.')
            ->line('Thank you for your understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your account registration has been rejected.',
            'reason' => $this->reason,
            'type' => 'account_rejection',
        ];
    }
}
