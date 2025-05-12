<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private array $paymentDetails
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.payment_received'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.payment_received', [
                'user' => $this->paymentDetails['user'],
                'amount' => $this->paymentDetails['amount'],
                'currency' => $this->paymentDetails['currency']
            ]))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.payment_received", ["user" => $this->paymentDetails['user']]),
            "type" => "payment_received",
            "amount" => $this->paymentDetails['amount'],
            "currency" => $this->paymentDetails['currency'],
            "payment_method" => $this->paymentDetails['method'],
            "project_id" => $this->paymentDetails['project_id'],
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}