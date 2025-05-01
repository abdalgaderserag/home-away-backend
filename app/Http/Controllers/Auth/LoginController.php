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
        $user = User::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->first();

        if ($user && !Hash::check($request->password, $user->password)) {
            if ($user->socialAccounts()->exists()) {
                return response([
                    'error' => 'This account was created with social login',
                    'available_providers' => $user->socialAccounts->pluck('provider')
                ], 422);
            }
            return response(['error' => 'Invalid credentials'], 401);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }
        $token = $user->createToken('authToken', [
            'remember' => $request->remember_me
        ])->plainTextToken;
        return response($token, Response::HTTP_OK);
    }

    public function logout(): Response
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();;
        return response("user logout completed", 200);
    }
}
