<?php

namespace App\Channels;

use App\Jobs\SendSmsJob;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toSms($notifiable);

        // Dispatch the SMS job to the queue
        SendSmsJob::dispatch($data['phone'], $data['message']);

        return true;
    }
}
