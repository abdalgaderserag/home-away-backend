<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentLinked extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $paymentMethod
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.payment_linked'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.payment_linked', ['method' => $this->paymentMethod]))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.payment_linked", ["method" => $this->paymentMethod]),
            "type" => "payment_linked",
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}