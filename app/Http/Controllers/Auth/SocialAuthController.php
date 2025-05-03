<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
    public function handleSocialLogin(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:google,facebook,apple',
            'token' => 'required|string',
        ]);

        switch ($request->provider) {
            case 'google':
                return $this->handleGoogleLogin($request);
            case 'facebook':
                return $this->handleFacebookLogin($request);
            case 'apple':
                return $this->handleAppleLogin($request);
            default:
                return response()->json(['error' => 'Invalid provider'], 400);
        }
    }

    private function handleGoogleLogin($request)
    {
        try {
            $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($request->token);
            if (!$payload) return response()->json(['error' => 'Invalid Google token'], 401);

            return $this->findOrCreateUser($payload, 'google', $payload['sub']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Google login failed'], 401);
        }
    }

    private function handleFacebookLogin($request)
    {
        try {
            $response = Http::get('https://graph.facebook.com/v15.0/me', [
                'access_token' => $request->token,
                'fields' => 'id,name,email,picture'
            ]);

            if ($response->failed()) return response()->json(['error' => 'Invalid Facebook token'], 401);

            $data = $response->json();
            return $this->findOrCreateUser([
                'name' => $data['name'],
                'email' => $data['email'],
                'avatar' => $data['picture']['data']['url'] ?? null,
            ], 'facebook', $data['id']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Facebook login failed'], 401);
        }
    }

    private function handleAppleLogin($request)
    {
        try {
            $jwt = JWT::decode($request->token, new Key(config('services.apple.public_key'), 'RS256'));
            $payload = (array)$jwt;
            
            return $this->findOrCreateUser([
                'email' => $payload['email'],
                'name' => $payload['email'],
            ], 'apple', $payload['sub']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Apple login failed'], 401);
        }
    }

    private function findOrCreateUser($socialUser, $provider, $providerId)
    {
        $user = User::where('provider_id', $providerId)
            ->orWhere('email', $socialUser['email'])
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser['name'] ?? $socialUser['email'],
                'email' => $socialUser['email'],
                'provider' => $provider,
                'provider_id' => $providerId,
                'email_verified_at' => now(),
                'avatar' => $socialUser['avatar'] ?? null,
            ]);
        }

        if (!$user->provider_id) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $providerId,
            ]);
        }

        $token = $user->createToken('social-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }


}
