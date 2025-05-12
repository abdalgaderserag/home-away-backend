<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportReplied extends Notification
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
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
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
