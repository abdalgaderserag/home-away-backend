<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Action\SendSmsAction;

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toSms($notifiable);

        $sms = new SendSmsAction();
        $sms->sendSms($data['phone'], $data['message']);
    }
}
