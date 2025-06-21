<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the mail configuration with Mailpit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            $user = User::first();
            if (!$user) {
                $this->error("No users found in the database.");
                return 1;
            }
            $email = $user->email;
        }

        $this->info("Testing mail configuration...");
        $this->line("Mail driver: " . config('mail.default'));
        $this->line("Mail host: " . config('mail.mailers.' . config('mail.default') . '.host'));
        $this->line("Mail port: " . config('mail.mailers.' . config('mail.default') . '.port'));
        $this->line("Sending test email to: {$email}");

        try {
            Mail::raw('This is a test email from Laravel using Mailpit!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Laravel Mailpit')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            $this->info("Test email sent successfully!");
            $this->line("Check Mailpit at: http://localhost:8025");
            $this->line("Or if using Docker: http://localhost:8025");
            
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 