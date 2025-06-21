<?php

namespace App\Action;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

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
        try {
            $formattedNumber = '+' . ltrim($to, '+');
            
            $result = $this->twilio->messages->create($formattedNumber, [
                'from' => config('services.twilio.from'),
                'body' => $message,
            ]);

            Log::info('SMS sent successfully', [
                'to' => $formattedNumber,
                'message_id' => $result->sid,
                'status' => $result->status
            ]);

            return $result;
        } catch (TwilioException $e) {
            Log::error('SMS sending failed', [
                'to' => $to,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error sending SMS', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
