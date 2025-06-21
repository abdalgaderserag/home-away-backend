<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationMain extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Always include database notifications
        $channels = ['database'];
        
        // Get user settings from cache or database
        $settings = Cache::remember(
            "user:{$notifiable->getKey()}:settings",
            now()->addDay(),
            fn() => $notifiable->settings
        );

        // Add mail notifications if enabled and user has email
        if ($settings && $settings->mail_notifications && $notifiable->email) {
            $channels[] = 'mail';
        }

        // Add SMS notifications if enabled and user has phone
        if ($settings && $settings->sms_notifications && $notifiable->phone) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    /**
     * Get the notification's default queue name.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'sms' => 'sms',
            'database' => 'notifications',
        ];
    }
}
