<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class IdVerificationDeclined extends NotificationMain
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.id_verification_declined'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.id_verification_declined'))
            ->line(__('notification.thank_you'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.id_verification_declined"),
            "type" => "id_verification",
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}
