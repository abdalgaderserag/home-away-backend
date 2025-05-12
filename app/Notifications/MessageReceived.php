<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageReceived extends Notification
{
    use Queueable;

    private Message $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.message_received', ['sender' => $this->message->sender->name]))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.message_received', ['sender' => $this->message->sender->name]))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.message_received", ["sender" => $this->message->sender->name]),
            "type" => "message",
            "message_id" => $this->message->id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}
