<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\Welcome;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use \App\Traits\OnboardsNewUser;

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $verificationCode = random_int(100000, 999999);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'] ?? null,
            'phone'             => $validated['phone'] ?? null,
            'password'          => Hash::make($validated['password']),
            'verification_code' => $verificationCode,
        ]);


        $this->onboardNewUser($user);


        // send verification notifications
        if ($user->email) {
            $user->sendEmailVerificationNotification();
        }
        if ($user->phone) {
            $user->sendPhoneVerificationNotification();
        }

        Auth::login($user);
        $token = $user->createToken("auth-token:{$user->id}")->plainTextToken;

        // send welcome notification
        $user->notify(new Welcome($user));

        return response()->json(['token' => $token, 'user' => $user], Response::HTTP_CREATED);
    }
}
