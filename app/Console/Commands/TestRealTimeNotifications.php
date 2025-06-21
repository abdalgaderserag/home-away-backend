<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Welcome;
use App\Services\RealTimeNotificationService;
use Illuminate\Console\Command;

class TestRealTimeNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:realtime {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the real-time notification system';

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

        $this->info("Testing real-time notifications for user: {$user->name} ({$user->email})");
        
        // Check Redis connection
        try {
            $redis = app('redis');
            $redis->ping();
            $this->info("✓ Redis connection successful");
        } catch (\Exception $e) {
            $this->error("✗ Redis connection failed: " . $e->getMessage());
            return 1;
        }

        // Check broadcasting configuration
        $broadcastDriver = config('broadcasting.default');
        $this->info("Broadcast driver: {$broadcastDriver}");

        // Send test notification
        $this->info("Sending test notification...");
        
        try {
            $user->notify(new Welcome($user));
            $this->info("✓ Test notification sent successfully!");
            
            // Get the latest notification
            $notification = $user->notifications()->latest()->first();
            
            if ($notification) {
                $this->info("Notification details:");
                $this->line("- ID: {$notification->id}");
                $this->line("- Type: " . ($notification->data['type'] ?? 'unknown'));
                $this->line("- Message: " . ($notification->data['message'] ?? 'No message'));
                $this->line("- Channel: user.{$user->id}");
                
                // Test real-time service
                $service = new RealTimeNotificationService();
                $service->sendToUser($user, $notification);
                $this->info("✓ Real-time notification broadcasted!");
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to send notification: " . $e->getMessage());
            return 1;
        }

        $this->info("\nTo test real-time notifications:");
        $this->line("1. Start queue workers: php artisan queue:work");
        $this->line("2. Connect to Redis channel: user.{$user->id}");
        $this->line("3. Send another notification to see real-time updates");

        return 0;
    }
} 