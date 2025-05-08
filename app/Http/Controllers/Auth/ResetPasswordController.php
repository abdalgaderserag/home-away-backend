<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate(['email_or_phone' => 'required']);

        $user = User::where('email', $request->email_or_phone)
            ->orWhere('phone', $request->email_or_phone)
            ->first();

        $status = Password::broker()->sendResetLink(
            ['email' => $user->email ?? $user->phone],
            function ($user, $token) use ($request) {
                if (filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)) {
                    $user->sendPasswordResetNotification($token);
                } else {
                    $user->sendPasswordResetSmsNotification($token);
                }
            }
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json($status)
            : response()->json($status, 400);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email_or_phone' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::broker()->reset(
            $request->only('email_or_phone', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password updated successfully'])
            : response()->json(['error' => 'Invalid token or expired link'], 400);
    }
}
