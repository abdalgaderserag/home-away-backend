<?php

namespace App\Observers;

use App\Events\NotificationSent;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationObserver
{
    /**
     * Handle the DatabaseNotification "created" event.
     */
    public function created(DatabaseNotification $notification): void
    {
        // Get the user who should receive this notification
        $user = User::find($notification->notifiable_id);
        
        if ($user) {
            // Broadcast the notification in real-time
            event(new NotificationSent($notification, $user));
        }
    }

    /**
     * Handle the DatabaseNotification "updated" event.
     */
    public function updated(DatabaseNotification $notification): void
    {
        // Broadcast notification updates (like read status)
        $user = User::find($notification->notifiable_id);
        
        if ($user) {
            event(new NotificationSent($notification, $user));
        }
    }

    /**
     * Handle the DatabaseNotification "deleted" event.
     */
    public function deleted(DatabaseNotification $notification): void
    {
        // Optionally broadcast deletion events
        $user = User::find($notification->notifiable_id);
        
        if ($user) {
            event(new NotificationSent($notification, $user));
        }
    }
} 