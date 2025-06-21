<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class RealTimeNotificationService
{
    /**
     * Send a real-time notification to a user.
     */
    public function sendToUser(User $user, $notification): void
    {
        event(new NotificationSent($notification, $user));
    }

    /**
     * Send real-time notifications to multiple users.
     */
    public function sendToUsers($users, $notification): void
    {
        foreach ($users as $user) {
            $this->sendToUser($user, $notification);
        }
    }

    /**
     * Broadcast notification update (read/unread status).
     */
    public function broadcastUpdate(DatabaseNotification $notification): void
    {
        $user = User::find($notification->notifiable_id);
        
        if ($user) {
            event(new NotificationSent($notification, $user));
        }
    }

    /**
     * Get user's private channel name.
     */
    public function getUserChannel(User $user): string
    {
        return 'user.' . $user->id;
    }

    /**
     * Get notification data for broadcasting.
     */
    public function getNotificationData($notification): array
    {
        return [
            'id' => $notification->id,
            'type' => $notification->data['type'] ?? 'notification',
            'message' => $notification->data['message'] ?? '',
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
        ];
    }
} 