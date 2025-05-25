<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ReferralCodeUsed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $referredUser;
    protected $bonusAmount;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $referredUser, float $bonusAmount)
    {
        $this->referredUser = $referredUser;
        $this->bonusAmount = $bonusAmount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        try {
            return (new MailMessage)
                ->subject('Your Referral Code Was Used!')
                ->greeting('Congratulations!')
                ->line('Your referral code was used by ' . $this->referredUser->name . ' to register.')
                ->line('You will receive ' . number_format($this->bonusAmount, 2) . ' CHF in bonus credits once their account is verified.')
                ->line('You can track your bonus credits in your dashboard.')
                ->action('View Bonus Credits', route('dashboard.bonus'))
                ->line('Thank you for helping grow our community!');
        } catch (\Exception $e) {
            Log::error('Error sending referral notification email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'referred_user_id' => $this->referredUser->id,
                'bonus_amount' => $this->bonusAmount,
            ]);

            throw $e;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        try {
            return [
                'referred_user_id' => $this->referredUser->id,
                'referred_user_name' => $this->referredUser->name,
                'bonus_amount' => $this->bonusAmount,
                'message' => 'Your referral code was used by ' . $this->referredUser->name,
            ];
        } catch (\Exception $e) {
            Log::error('Error creating notification array', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'referred_user_id' => $this->referredUser->id,
                'bonus_amount' => $this->bonusAmount,
            ]);

            throw $e;
        }
    }
}
