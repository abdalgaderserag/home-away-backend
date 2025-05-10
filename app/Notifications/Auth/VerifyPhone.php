<?php

namespace App\Notifications\Auth;

use App\Action\SendSmsAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VerifyPhone extends Notification
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
        return ['sms'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSms(object $notifiable)
    {
        $app_name = config('app.name');
        $verification_code = $notifiable->verification_code;
        $phone = $notifiable->phone;

        return [
            'phone' => $phone,
            'message' => "Please use the following verification code to verify your phone number: {$verification_code}. Thank you for using {$app_name}!"
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
