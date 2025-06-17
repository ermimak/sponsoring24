<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
class NewUserRegistration extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The rejection reason.
     *
     * @var string
     */
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
            ->line('We are happy to inform you that your account registration has been approved.')
            ->line('Reason:')
            ->line($this->user->name . ' ' . $this->user->email . ' ' . $this->user->referral_code)
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
            'message' => 'Your account registration has been approved.',
            'user' => $this->user,
            'type' => 'account_approval',
        ];
    }
}
