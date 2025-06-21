<?php

namespace App\Jobs;

use App\Action\SendSmsAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $phone,
        private string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $smsAction = new SendSmsAction();
            $smsAction->sendSms($this->phone, $this->message);
        } catch (\Exception $e) {
            Log::error('SMS job failed', [
                'phone' => $this->phone,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SMS job failed permanently', [
            'phone' => $this->phone,
            'error' => $exception->getMessage()
        ]);
    }
} 