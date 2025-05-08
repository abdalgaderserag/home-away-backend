<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\OnboardsNewUser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    use OnboardsNewUser;
    public function redirect($provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook', 'apple']), Response::HTTP_BAD_REQUEST, 'Invalid provider');
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback($provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook', 'apple']), 400, 'Invalid provider');
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            return $this->processSocialLogin([
                'id'     => $socialUser->getId(),
                'email'  => $socialUser->getEmail(),
                'name'   => $socialUser->getName(),
                'avatar' => $socialUser->getAvatar(),
            ], $provider);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Social callback failed', 'message' => $e->getMessage()], 401);
        }
    }
    public function socialLogin(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|in:google,facebook,apple',
            'token'    => 'required|string',
        ]);

        try {
            switch ($data['provider']) {
                case 'google':
                    $info = $this->verifyGoogleToken($data['token']);
                    break;
                case 'facebook':
                    $info = $this->verifyFacebookToken($data['token']);
                    break;
                case 'apple':
                    $info = $this->verifyAppleToken($data['token']);
                    break;
            }
            if (empty($info['email'])) {
                return response()->json(['message' => 'Email is required'], 422);
            }
            return $this->processSocialLogin($info, $data['provider']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Social login failed', 'message' => $e->getMessage()], 401);
        }
    }
    protected function processSocialLogin(array $social, string $provider)
    {
        $user = User::where('provider_id', $social['id'])->first()
            ?? User::where('email', $social['email'])->first();

        if (! $user) {
            $user = User::create([
                'name'              => $social['name'] ?? $social['email'],
                'email'             => $social['email'],
                'phone'             => null,
                'password'          => Hash::make(Str::random(60)),
                'provider'          => $provider,
                'provider_id'       => $social['id'],
                'email_verified_at' => now(),
                'avatar'            => $social['avatar'] ?? null,
            ]);
            $this->onboardNewUser($user);
        }

        // ensure provider fields
        if (! $user->provider_id) {
            $user->update(['provider' => $provider, 'provider_id' => $social['id']]);
        }

        $token = $user->createToken("auth-token:{$user->id}")->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user], Response::HTTP_OK);
    }

    private function verifyGoogleToken($token)
    {
        $res = Http::get("https://oauth2.googleapis.com/tokeninfo?id_token={$token}");
        if ($res->failed()) return [];
        $d = $res->json();
        return ['id' => $d['sub'] ?? null, 'email' => $d['email'] ?? null, 'name' => $d['name'] ?? null];
    }

    private function verifyFacebookToken($token)
    {
        $res = Http::get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email',
            'access_token' => $token
        ]);
        if ($res->failed()) return [];
        return $res->json();
    }

    private function verifyAppleToken($jwt)
    {
        $hdr = json_decode(base64_decode(explode('.', $jwt)[0]), true);
        $jwkSet = Http::get('https://appleid.apple.com/auth/keys')->json();
        $keys = JWK::parseKeySet($jwkSet);
        try {
            $payload = JWT::decode($jwt, $keys[$hdr['kid']] ?? null);
            return ['id' => $payload->sub, 'email' => $payload->email, 'name' => null];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
