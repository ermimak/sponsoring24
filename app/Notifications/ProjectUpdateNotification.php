<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected $updateType;
    protected $details;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, string $updateType, array $details = [])
    {
        $this->project = $project;
        $this->updateType = $updateType;
        $this->details = $details;
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
        $mailMessage = (new MailMessage)
            ->subject('Project Update: ' . $this->project->name)
            ->greeting('Hello ' . $notifiable->name . '!');

        switch ($this->updateType) {
            case 'created':
                $mailMessage->line('A new project has been created: ' . $this->project->name)
                    ->line('Check out the details and get involved!');
                break;
            case 'updated':
                $mailMessage->line('The project "' . $this->project->name . '" has been updated.')
                    ->line('Some details of the project have been changed.');
                break;
            case 'completed':
                $mailMessage->line('The project "' . $this->project->name . '" has been marked as completed.')
                    ->line('Thank you for your involvement in this project!');
                break;
            default:
                $mailMessage->line('There has been an update to the project "' . $this->project->name . '".');
        }

        return $mailMessage
            ->action('View Project', url('/dashboard/projects'))
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
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'update_type' => $this->updateType,
            'details' => $this->details,
            'message' => $this->getMessage(),
            'type' => 'project_update',
        ];
    }

    /**
     * Get the appropriate message based on update type
     */
    protected function getMessage(): string
    {
        switch ($this->updateType) {
            case 'created':
                return 'New project created: ' . $this->project->name;
            case 'updated':
                return 'Project "' . $this->project->name . '" has been updated';
            case 'completed':
                return 'Project "' . $this->project->name . '" has been marked as completed';
            default:
                return 'Project "' . $this->project->name . '" has been updated';
        }
    }
}
