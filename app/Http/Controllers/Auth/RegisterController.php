<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
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
        $user->save();
        if ($request->email) {
            $user->sendEmailVerificationNotification();
        }
        if ($request->phone) {
            $user->sendPhoneVerificationNotification();
        }
        $token = $user->createToken("token:" . $user->id);
        Auth::login($user);
        return response($token->plainTextToken, 201);
    }
}
