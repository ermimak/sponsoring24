<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPendingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
            ->subject('Your Account Registration is Pending Approval')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for registering with Sponsoring24.')
            ->line('Your account has been created but requires approval from an administrator before you can log in.')
            ->line('You will receive another email once your account has been approved or if additional information is needed.')
            ->line('Thank you for your patience!')
            ->salutation('Regards, The Sponsoring24 Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your account registration is pending approval.',
            'type' => 'account_pending',
        ];
    }
}
