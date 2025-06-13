<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Rated extends NotificationMain
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
