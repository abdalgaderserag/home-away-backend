<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SupportReplied extends NotificationMain
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.support_reply', ['ticket_title' => $this->ticketTitle]))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.support_reply', ['ticket_title' => $this->ticketTitle]))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.support_reply", ["ticket_title" => $this->ticketTitle]),
            "type" => "support",
            "ticket_id" => $this->ticketId,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}
