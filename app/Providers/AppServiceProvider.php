<?php

namespace App\Providers;

use App\Channels\SmsChannel;
use App\Models\User;
use App\Observers\NotificationObserver;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)->extend('sms', function ($app) {
            return new SmsChannel();
        });

        // Register notification observer for real-time broadcasting
        DatabaseNotification::observe(NotificationObserver::class);

        Gate::define('viewPulse', function (User $user) {
            return false;
            return $user->hasRole('admin');
        });
    }
}
