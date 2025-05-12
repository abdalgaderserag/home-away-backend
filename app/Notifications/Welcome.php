<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Welcome extends Notification
{
    use Queueable;

    private User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
            ->subject(__('notification.welcome', ['name' => $this->user->name]))
            ->greeting(__('notification.hello', ['name' => $this->user->name]))
            ->line(__('notification.welcome', [
                'app_name' => config('app.name'),
                'name' => $this->user->name
            ]))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.welcome", [
                'app_name' => config('app.name'),
                'name' => $this->user->name
            ]),
            "type" => "welcome",
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}
