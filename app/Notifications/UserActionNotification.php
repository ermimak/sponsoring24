<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserActionNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $action;
    protected $actionData;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param string $action
     * @param array $actionData
     */
    public function __construct(User $user, string $action, array $actionData = [])
    {
        $this->user = $user;
        $this->action = $action;
        $this->actionData = $actionData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = $this->getSubject();
        $message = $this->getMessage();

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->getSubject(),
            'message' => $this->getMessage(),
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'action' => $this->action,
            'action_data' => $this->actionData,
            'time' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the notification subject based on the action.
     *
     * @return string
     */
    protected function getSubject()
    {
        switch ($this->action) {
            case 'created':
                return 'New User Account Created';
            case 'updated':
                return 'User Account Updated';
            case 'deleted':
                return 'User Account Deleted';
            case 'role_changed':
                return 'User Role Changed';
            case 'approved':
                return 'User Account Approved';
            case 'rejected':
                return 'User Account Rejected';
            default:
                return 'User Account Action';
        }
    }

    /**
     * Get the notification message based on the action.
     *
     * @return string
     */
    protected function getMessage()
    {
        switch ($this->action) {
            case 'created':
                return "A new user account has been created for {$this->user->name} ({$this->user->email}).";
            case 'updated':
                $updatedFields = isset($this->actionData['updated_fields']) ? implode(', ', $this->actionData['updated_fields']) : 'profile information';
                return "User {$this->user->name} has updated their {$updatedFields}.";
            case 'deleted':
                return "User account for {$this->user->name} ({$this->user->email}) has been deleted.";
            case 'role_changed':
                $roles = isset($this->actionData['roles']) ? implode(', ', $this->actionData['roles']) : 'unknown roles';
                return "User {$this->user->name} has been assigned the following roles: {$roles}.";
            case 'approved':
                return "User account for {$this->user->name} ({$this->user->email}) has been approved.";
            case 'rejected':
                $reason = isset($this->actionData['reason']) ? " Reason: {$this->actionData['reason']}" : '';
                return "User account for {$this->user->name} ({$this->user->email}) has been rejected.{$reason}";
            default:
                return "An action has been performed on user account {$this->user->name} ({$this->user->email}).";
        }
    }
}
