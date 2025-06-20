<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
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
        Sanctum::authenticateAccessTokensUsing(function (PersonalAccessToken $token, $isValid) {
            if ($token->can('remember') && $isValid) {
                return true;
            }
            return $token->created_at->gt(now()->subMinutes(config('sanctum.expiration')));
        });


        Gate::before(function (User $user, $ability) {
            return $user->hasRole('admin');
        });
    }
}
