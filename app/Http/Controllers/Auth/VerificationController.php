<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)
            ->where('verification_code', $request->code)
            ->first();


        if (!$user) {
            return response()->json(
                ['message' => 'Invalid verification code'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(
                ['message' => 'Email already verified'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->update([
            'email_verified_at' => now(),
            'verification_code' => null
        ]);

        return response()->json(['message' => 'Email verified successfully']);
    }
    public function emailResend()
    {
        $user = Auth::user();
        $user->sendEmailVerificationNotification();
        return response('Email verification code has been sended', Response::HTTP_OK);
    }
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'phone' => 'required|string'
        ]);


        $user = User::where('phone', $request->phone)
            ->where('verification_code', $request->code)
            ->first();

        if (!$user) {
            return response()->json(
                ['message' => 'Invalid verification code'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($user->hasVerifiedPhone()) {
            return response()->json(
                ['message' => 'Phone already verified'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->update([
            'phone_verified_at' => now(),
            'verification_code' => null
        ]);

        return response()->json(['message' => 'Phone verified successfully']);
    }
    public function phoneResend()
    {
        $user = Auth::user();
        $user->sendPhoneVerificationNotification();
        return response('Phone verification code has been sended', Response::HTTP_OK);
    }
}
