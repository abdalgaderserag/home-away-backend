<?php

namespace App\Http\Controllers\Auth;

use App\Enum\User\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\User\Bio;
use App\Models\User\Settings;
use App\Notifications\Welcome;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): Response
    {
        $user = new User();
        $user->name = $request->name;
        if ($request->email) {
            $user->email = $request->email;
        } else {
            $user->phone = $request->phone;
        }
        $user->password = Hash::make($request->password);
        if ($request->type === UserType::Designer->value) {
            $user->type = UserType::Designer->value;
        }
        $user->save();

        // create user settings
        $settings = new Settings();
        $settings->user_id = $user->id;
        $settings->save();

        // create user bio
        $bio = new Bio();
        $bio->user_id = $user->id;
        $bio->save();

        // send verification request
        if ($request->email) {
            $user->sendEmailVerificationNotification();
        }
        if ($request->phone) {
            $user->sendPhoneVerificationNotification();
        }
        $token = $user->createToken("token:" . $user->id);
        Auth::login($user);
        $user->notify(new Welcome);
        return response($token->plainTextToken, 201);
    }
}
