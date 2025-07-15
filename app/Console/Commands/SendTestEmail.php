<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a test email to verify the mail configuration.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $testEmail = 'test-sponsoring24@mailtrap.io';
            $this->info("Attempting to send a test email to {$testEmail}...");

            Mail::raw('This is a test email to verify your Mailtrap configuration is working correctly.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Mailtrap Test from Sponsoring24');
            });

            $this->info("Successfully sent the test email. Please check your Mailtrap inbox at https://mailtrap.io/inboxes.");
            return 0;
        } catch (Exception $e) {
            $this->error('Failed to send the test email.');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
