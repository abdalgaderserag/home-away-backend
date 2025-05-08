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
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->input('email'))
            ->orWhere('phone', $request->input('phone'))
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            if ($user && $user->socialAccounts()->exists()) {
                return response()->json([
                    'error' => 'This account uses social login only',
                    'providers' => $user->socialAccounts->pluck('provider'),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return response()->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken("auth-token:{$user->id}")->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user], Response::HTTP_OK);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout successful'], Response::HTTP_OK);
    }
}
