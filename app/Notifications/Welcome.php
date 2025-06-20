<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioMmsMessage;

class Welcome extends NotificationMain
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

    public function toTwilio($notifiable)
    {
        $app_name = config('app.name');
        $verification_code = $notifiable->verification_code;
        return (new TwilioMmsMessage())
            ->content("Please use the following verification code to verify your phone number: {$verification_code}. Thank you for using {$app_name}!");
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
            ->action(__('notification.get_started'), route('dashboard'))
            ->line(__('notification.thank_you'));
    }

    public function toSms(object $notifiable)
    {
        $app_name = config('app.name');
        $user_name = $this->user->name;

        return [
            'phone' => $notifiable->phone,
            'message' => "Welcome to {$app_name}, {$user_name}! We're excited to have you on board. Get started by exploring our platform."
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.welcome", ["name" => $this->user->name]),
            "type" => "welcome",
            "user_id" => $this->user->id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}
