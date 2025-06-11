<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContentActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $content;
    protected $action;
    protected $actionData;
    protected $contentType;

    /**
     * Create a new notification instance.
     *
     * @param mixed $content
     * @param string $contentType
     * @param string $action
     * @param array $actionData
     */
    public function __construct($content, string $contentType, string $action, array $actionData = [])
    {
        $this->content = $content;
        $this->contentType = $contentType;
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
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($message)
            ->action('View Content', $this->getActionUrl())
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
            'content_id' => $this->content->id ?? null,
            'content_title' => $this->content->title ?? null,
            'content_type' => $this->contentType,
            'action' => $this->action,
            'action_data' => $this->actionData,
            'time' => now()->toIso8601String(),
            'type' => 'content_action',
        ];
    }

    /**
     * Get the notification subject based on the action.
     *
     * @return string
     */
    protected function getSubject()
    {
        $contentTypeName = ucfirst($this->contentType);
        
        switch ($this->action) {
            case 'created':
                return "New {$contentTypeName} Created";
            case 'updated':
                return "{$contentTypeName} Updated";
            case 'deleted':
                return "{$contentTypeName} Deleted";
            case 'published':
                return "{$contentTypeName} Published";
            case 'unpublished':
                return "{$contentTypeName} Unpublished";
            default:
                return "{$contentTypeName} Action";
        }
    }

    /**
     * Get the notification message based on the action.
     *
     * @return string
     */
    protected function getMessage()
    {
        $contentTypeName = ucfirst($this->contentType);
        $title = $this->content->title ?? 'Untitled';
        $updatedBy = isset($this->actionData['updated_by']) ? " by {$this->actionData['updated_by']}" : '';
        
        switch ($this->action) {
            case 'created':
                return "A new {$this->contentType} titled \"{$title}\" has been created{$updatedBy}.";
            case 'updated':
                $updatedFields = isset($this->actionData['updated_fields']) ? implode(', ', $this->actionData['updated_fields']) : 'content';
                return "The {$this->contentType} \"{$title}\" has been updated{$updatedBy}. Updated fields: {$updatedFields}.";
            case 'deleted':
                return "The {$this->contentType} \"{$title}\" has been deleted{$updatedBy}.";
            case 'published':
                return "The {$this->contentType} \"{$title}\" has been published{$updatedBy}.";
            case 'unpublished':
                return "The {$this->contentType} \"{$title}\" has been unpublished{$updatedBy}.";
            default:
                return "An action has been performed on the {$this->contentType} \"{$title}\"{$updatedBy}.";
        }
    }

    /**
     * Get the action URL for the notification.
     *
     * @return string
     */
    protected function getActionUrl()
    {
        switch ($this->contentType) {
            case 'news':
                return url('/admin/content/news');
            case 'project':
                return url('/admin/projects');
            case 'page':
                return url('/admin/content/pages');
            default:
                return url('/admin/dashboard');
        }
    }
}
