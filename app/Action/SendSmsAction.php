<?php

namespace App\Action;


use Twilio\Rest\Client;

class SendSmsAction
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendSms($to, $message)
    {
        $formattedNumber = '+' . ltrim($to, '+');
        return $this->twilio->messages->create($formattedNumber, [
            'from' => config('services.twilio.from'),
            'body' => $message,
        ]);
    }
}
