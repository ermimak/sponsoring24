<?php

namespace App\Notifications;

use App\Models\BonusCredit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralBonusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $bonusCredit;

    /**
     * Create a new notification instance.
     *
     * @param BonusCredit $bonusCredit
     * @return void
     */
    public function __construct(BonusCredit $bonusCredit)
    {
        $this->bonusCredit = $bonusCredit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $referredUser = $this->bonusCredit->referredUser;
        
        return (new MailMessage)
            ->subject('You Earned a Referral Bonus!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Your referral has paid off.')
            ->line($referredUser->name . ' has purchased a license using your referral link.')
            ->line('We have credited CHF 100 to your account as a referral bonus.')
            ->action('View Your Referrals', url('/dashboard/referrals'))
            ->line('Thank you for spreading the word about Sponsoring24!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $referredUser = $this->bonusCredit->referredUser;
        
        return [
            'title' => 'Referral Bonus Received',
            'message' => 'You received CHF 100 bonus credit because ' . $referredUser->name . ' purchased a license using your referral.',
            'type' => 'referral_bonus',
            'bonus_credit_id' => $this->bonusCredit->id,
        ];
    }
}
