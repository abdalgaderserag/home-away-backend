<?php

namespace App\Notifications\Payment;

use App\Models\Milestone;
use App\Notifications\NotificationMain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentDisposal extends NotificationMain implements ShouldQueue
{
    use Queueable;

    public function __construct(private Milestone $milestone) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.payment_disposal'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.payment_disposal'))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.payment_disposal"),
            "type" => "payment_disposal",
            "milestone_id" => $this->milestone->id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}