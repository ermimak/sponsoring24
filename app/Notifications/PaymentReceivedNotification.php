<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $amount;
    protected $projectName;

    /**
     * Create a new notification instance.
     */
    public function __construct($payment, float $amount, string $projectName = null)
    {
        $this->payment = $payment;
        $this->amount = $amount;
        $this->projectName = $projectName;
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
        $message = (new MailMessage)
            ->subject('Payment Received')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We have received a payment of CHF ' . number_format($this->amount, 2) . '.');
        
        if ($this->projectName) {
            $message->line('This payment is for the project: ' . $this->projectName);
        }
        
        return $message
            ->line('Thank you for your support!')
            ->action('View Details', url('/dashboard'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'payment_id' => $this->payment->id ?? null,
            'amount' => $this->amount,
            'message' => 'Payment of CHF ' . number_format($this->amount, 2) . ' received',
            'type' => 'payment_received',
        ];
        
        if ($this->projectName) {
            $data['project_name'] = $this->projectName;
            $data['message'] .= ' for project ' . $this->projectName;
        }
        
        return $data;
    }
}
