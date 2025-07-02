<?php

namespace App\Services;

use App\Models\BonusCredit;
use App\Models\User;
use App\Notifications\BonusCreditNotification;
use App\Notifications\ReferralBonusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Credit a referral bonus when a license is purchased
     * Uses ACID transaction to ensure data integrity
     * 
     * @param User $user The user who purchased the license (referred user)
     * @param float $amount The amount of the purchase
     * @param string $currency The currency of the purchase
     * @param string $paymentId The payment ID from Stripe
     * @return array|null Returns bonus credit info or null if no referral found
     */
    public function creditReferralBonus(User $user, float $amount, string $currency, string $paymentId): ?array
    {
        try {
            // Find any pending referral bonus for this user
            // Note: We're not filtering by type='referral' here because the BonusCredit might not have the type set
            // when created during registration
            $bonusCredit = BonusCredit::where('referred_user_id', $user->id)
                ->where(function($query) {
                    $query->where('type', 'referral')
                          ->orWhereNull('type');
                })
                ->where(function($query) {
                    $query->where('status', 'pending')
                          ->orWhereNull('status');
                })
                ->where(function($query) {
                    $query->where('credited', false)
                          ->orWhereNull('credited');
                })
                ->first();
                
            if (!$bonusCredit) {
                Log::info('No pending referral bonus found for user', [
                    'user_id' => $user->id,
                    'payment_id' => $paymentId
                ]);
                return null;
            }
            
            // Find the referrer
            $referrer = User::find($bonusCredit->user_id);
            if (!$referrer) {
                Log::error('Referrer not found for bonus credit', [
                    'bonus_credit_id' => $bonusCredit->id,
                    'referrer_id' => $bonusCredit->user_id,
                    'referred_user_id' => $user->id
                ]);
                return null;
            }
            
            // Use a transaction to ensure ACID compliance
            DB::beginTransaction();
            
            try {
                // Update the bonus credit
                $bonusCredit->update([
                    'status' => 'credited',
                    'credited' => true,
                    'amount' => 100.00, // CHF 100 bonus
                    'payment_id' => $paymentId,
                    'credited_at' => now()
                ]);
                
                // Commit the transaction
                DB::commit();
                
                // Log the successful credit
                Log::info('Referral bonus credited successfully', [
                    'bonus_credit_id' => $bonusCredit->id,
                    'referrer_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'amount' => $bonusCredit->amount,
                    'payment_id' => $paymentId
                ]);
                
                // Send notifications outside of the transaction
                $this->sendBonusNotifications($bonusCredit, $referrer, $user);
                
                return [
                    'bonus_credit_id' => $bonusCredit->id,
                    'referrer_id' => $referrer->id,
                    'amount' => $bonusCredit->amount,
                    'currency' => $currency
                ];
                
            } catch (\Exception $e) {
                // Rollback the transaction if anything fails
                DB::rollBack();
                
                Log::error('Failed to credit referral bonus', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'bonus_credit_id' => $bonusCredit->id,
                    'user_id' => $user->id,
                    'payment_id' => $paymentId
                ]);
                
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error in creditReferralBonus', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'payment_id' => $paymentId
            ]);
            
            return null;
        }
    }
    
    /**
     * Send notifications about credited bonus
     * 
     * @param BonusCredit $bonusCredit
     * @param User $referrer
     * @param User $referredUser
     * @return void
     */
    private function sendBonusNotifications(BonusCredit $bonusCredit, User $referrer, User $referredUser): void
    {
        try {
            // Notify the referrer about their bonus
            $referrer->notify(new BonusCreditNotification($bonusCredit, 'credited', [
                'currency' => 'CHF',
                'processed_at' => now()->toDateTimeString(),
                'amount' => $bonusCredit->amount,
                'referred_name' => $referredUser->name,
                'referred_email' => $referredUser->email
            ]));
            
            // Also send the specialized referral bonus notification
            $referrer->notify(new ReferralBonusNotification($bonusCredit));
            
            // Notify admin users about the bonus credit
            $admins = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new BonusCreditNotification($bonusCredit, 'credited', [
                    'currency' => 'CHF',
                    'processed_at' => now()->toDateTimeString(),
                    'amount' => $bonusCredit->amount,
                    'user_name' => $referrer->name,
                    'user_email' => $referrer->email,
                    'referred_user_name' => $referredUser->name,
                    'referred_user_email' => $referredUser->email
                ]));
            }
            
            Log::info('Bonus credit notifications sent', [
                'bonus_credit_id' => $bonusCredit->id,
                'referrer_id' => $referrer->id,
                'referred_user_id' => $referredUser->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bonus notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'bonus_credit_id' => $bonusCredit->id
            ]);
        }
    }
}
