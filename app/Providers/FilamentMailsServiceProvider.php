<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Vormkracht10\FilamentMails\Facades\FilamentMails;

class FilamentMailsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register filament-mails routes
        if (class_exists(FilamentMails::class)) {
            FilamentMails::routes();
        }

        // Publish filament-mails assets if not already published
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../../vendor/vormkracht10/filament-mails/config/filament-mails.php' => config_path('filament-mails.php'),
        ], 'filament-mails-config');

        $this->publishes([
            __DIR__.'/../../vendor/vormkracht10/filament-mails/database/migrations' => database_path('migrations'),
        ], 'filament-mails-migrations');
    }
} 