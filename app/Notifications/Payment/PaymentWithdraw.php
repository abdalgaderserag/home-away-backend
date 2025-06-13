<?php

namespace App\Notifications\Payment;

use App\Notifications\NotificationMain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentWithdraw extends NotificationMain implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private float $amount
    ) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.payment_withdraw'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.payment_withdraw', ['amount' => $this->amount]))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.payment_withdraw"),
            "type" => "payment_withdraw",
            "amount" => $this->amount,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}