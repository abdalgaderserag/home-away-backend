<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Rated extends Notification
{
    use Queueable;

    private User $rater;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->rater = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.designer_rate', ['designer' => $this->rater->name]))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.designer_rate', ['designer' => $this->rater->name]))
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
            "message" => __('notification.designer_rate', ["designer" => $this->rater->name]),
            "type" => "rating",
            "rater_id" => $this->rater->id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}
