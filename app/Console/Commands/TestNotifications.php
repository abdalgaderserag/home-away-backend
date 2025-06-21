<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Welcome;
use Illuminate\Console\Command;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
        } else {
            $user = User::first();
            if (!$user) {
                $this->error("No users found in the database.");
                return 1;
            }
        }

        $this->info("Testing notifications for user: {$user->name} ({$user->email})");
        
        // Check user settings
        $settings = $user->settings;
        $this->info("User settings:");
        $this->line("- Mail notifications: " . ($settings->mail_notifications ? 'Enabled' : 'Disabled'));
        $this->line("- SMS notifications: " . ($settings->sms_notifications ? 'Enabled' : 'Disabled'));
        $this->line("- Language: {$settings->lang}");

        // Send test notification
        $this->info("Sending test notification...");
        
        try {
            $user->notify(new Welcome($user));
            $this->info("Test notification sent successfully!");
            
            // Show what channels would be used
            $notification = new Welcome($user);
            $channels = $notification->via($user);
            $this->info("Channels used: " . implode(', ', $channels));
            
        } catch (\Exception $e) {
            $this->error("Failed to send notification: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}