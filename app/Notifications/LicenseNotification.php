<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $type;
    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param mixed $user
     * @param string $type
     * @param array $data
     */
    public function __construct($user, string $type, array $data = [])
    {
        $this->user = $user;
        $this->type = $type;
        $this->data = $data;
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

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($message);
            
        // Add license key information for purchase success notifications
        if ($this->type === 'purchase_success' && isset($this->data['license_key'])) {
            $licenseKey = $this->data['license_key'];
            $expiresAt = $this->data['expires_at'] ?? 'one year from today';
            
            $mailMessage->line("Your license key is: **{$licenseKey}**")
                ->line("Your license will expire on: **{$expiresAt}**")
                ->line("Please keep this information for your records.");
        }
        
        return $mailMessage
            ->action('View License Details', url('/user/license'))
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
            'type' => $this->type,
            'data' => $this->data,
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
        switch ($this->type) {
            case 'purchase_success':
                return 'Your Sponsoring24 License Purchase Was Successful';
            case 'license_expiring':
                return 'Your Sponsoring24 License Is About to Expire';
            case 'license_expired':
                return 'Your Sponsoring24 License Has Expired';
            case 'license_renewed':
                return 'Your Sponsoring24 License Has Been Renewed';
            default:
                return 'Sponsoring24 License Notification';
        }
    }

    /**
     * Get the notification message based on the action.
     *
     * @return string
     */
    protected function getMessage()
    {
        $currency = $this->data['currency'] ?? 'CHF';
        $amount = $this->data['amount'] ?? 0;
        
        switch ($this->type) {
            case 'purchase_success':
                $discountMessage = '';
                if (!empty($this->data['discount_applied']) && $this->data['discount_applied']) {
                    $discountAmount = $this->data['discount_amount'] ?? 0;
                    $discountMessage = " A discount of {$currency} {$discountAmount} was applied to your purchase.";
                }
                return "Your annual Sponsoring24 license purchase for {$currency} {$amount} was successful.{$discountMessage} Your license is now active.";
            
            case 'license_expiring':
                $daysLeft = $this->data['days_left'] ?? 30;
                return "Your Sponsoring24 license will expire in {$daysLeft} days. Please renew your license to continue using all features.";
            
            case 'license_expired':
                return "Your Sponsoring24 license has expired. Please renew your license to continue using all features.";
            
            case 'license_renewed':
                return "Your Sponsoring24 license has been renewed for another year. Thank you for your continued support!";
            
            default:
                return "This is a notification regarding your Sponsoring24 license.";
        }
    }
}
