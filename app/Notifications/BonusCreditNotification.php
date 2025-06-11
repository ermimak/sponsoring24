<?php

namespace App\Notifications;

use App\Models\BonusCredit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BonusCreditNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $bonusCredit;
    protected $action;
    protected $actionData;

    /**
     * Create a new notification instance.
     *
     * @param BonusCredit $bonusCredit
     * @param string $action
     * @param array $actionData
     */
    public function __construct(BonusCredit $bonusCredit, string $action, array $actionData = [])
    {
        $this->bonusCredit = $bonusCredit;
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
            ->action('View Bonus Credits', url('/dashboard/bonus'))
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
            'bonus_credit_id' => $this->bonusCredit->id,
            'amount' => $this->bonusCredit->amount,
            'status' => $this->bonusCredit->status,
            'action' => $this->action,
            'action_data' => $this->actionData,
            'time' => now()->toIso8601String(),
            'type' => 'bonus_credit',
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
                return 'New Bonus Credit Added';
            case 'credited':
                return 'Bonus Credit Processed';
            case 'referral_used':
                return 'Your Referral Code Was Used';
            default:
                return 'Bonus Credit Update';
        }
    }

    /**
     * Get the notification message based on the action.
     *
     * @return string
     */
    protected function getMessage()
    {
        $amount = number_format($this->bonusCredit->amount, 2);
        $currency = $this->actionData['currency'] ?? 'CHF';
        
        switch ($this->action) {
            case 'created':
                return "A new bonus credit of {$currency} {$amount} has been added to your account.";
            case 'credited':
                return "Your bonus credit of {$currency} {$amount} has been processed and credited to your account.";
            case 'referral_used':
                $referredName = $this->actionData['referred_name'] ?? 'someone';
                return "{$referredName} used your referral code. A bonus credit of {$currency} {$amount} has been added to your account.";
            default:
                return "Your bonus credit of {$currency} {$amount} has been updated.";
        }
    }
}
