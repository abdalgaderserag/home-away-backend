<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request): Response
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken("token:" . $user->id);
            return response(['token' => $token->plainTextToken]);
        }
        return response(['error' => "password and email didn't match"],401);
    }

    public function logout(): Response
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();;
        return response("user logout completed", 200);
    }
}
