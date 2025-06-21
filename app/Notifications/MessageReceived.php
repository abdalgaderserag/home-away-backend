<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MessageReceived extends NotificationMain
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.message_received', ['sender' => $this->message->sender->name]))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.message_received', ['sender' => $this->message->sender->name]))
            ->action(__('notification.view_message'), route('messages.show', $this->message->id))
            ->line(__('notification.thank_you'));
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable)
    {
        $app_name = config('app.name');
        $sender_name = $this->message->sender->name;

        return [
            'phone' => $notifiable->phone,
            'message' => "You have received a new message from {$sender_name} on {$app_name}. Please check your inbox."
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.message_received", ["sender" => $this->message->sender->name]),
            "type" => "message",
            "message_id" => $this->message->id,
            "sender_id" => $this->message->sender_id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}
