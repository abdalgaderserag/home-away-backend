<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use ApiResponse;
    public function redirect($provider)
    {
        $validProviders = ['google', 'facebook', 'apple'];

        if (!in_array($provider, $validProviders)) {
            return response()->json(['error' => 'Invalid provider'], 400);
        }

        return Socialite::driver($provider)
            ->stateless()
            ->redirect();
    }

    public function callback($provider)
    {
        try {
            $providerUser = Socialite::driver($provider)->stateless()->user();

            // Find existing user by email or create new
            $user = User::firstOrNew(['email' => $providerUser->getEmail()]);

            if (!$user->exists) {
                $user->fill([
                    'name' => $providerUser->getName(),
                    // todo : fix this
                    'password' => Hash::make(rand(24)),
                    'email_verified_at' => now(),
                ])->save();
            }

            // Link social account
            $user->socialLogin($providerUser, $provider);

            return $this->authResponse($user, 'social-login');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Social login failed',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
