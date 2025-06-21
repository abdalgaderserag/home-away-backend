<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class StartQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queues:start {--daemon : Run in daemon mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start all necessary queue workers for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting queue workers...');

        $queues = [
            'default' => 'default',
            'emails' => 'emails',
            'sms' => 'sms',
            'notifications' => 'notifications',
        ];

        foreach ($queues as $name => $queue) {
            $this->info("Starting {$name} queue worker...");
            
            $command = "php artisan queue:work --queue={$queue} --tries=3 --timeout=60";
            
            if ($this->option('daemon')) {
                $command .= ' --daemon';
            }

            if (app()->environment('production')) {
                Process::run($command);
            } else {
                $this->line("Command: {$command}");
            }
        }

        $this->info('All queue workers started successfully!');
    }
} 