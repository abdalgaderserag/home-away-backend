<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class NotificationMain extends Notification
{

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        $setting = Cache::remember(
            "user:{$notifiable->getKey()}:settings",
            now()->addDay(),
            fn() => $notifiable->setting
        );

        if ($setting->mail_notifications) {
            $channels[] = 'mail';
        }

        if ($setting->sms_notifications) {
            $channels[] = 'sms';
        }

        return $channels;
    }
}
