<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerificationCodeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function verifyEmail(VerificationCodeRequest $request)
    {

        $user = Auth::user();


        if (!$user || $user->verification_code !== $request->code) {
            return response()->json(
                ['message' => 'Invalid verification code'],
                Response::HTTP_UNPROCESSABLE_ENTITY
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


    public function verifyPhone(VerificationCodeRequest $request)
    {

        $user = Auth::user();

        if ($user->hasVerifiedPhone()) {
            return response()->json(
                ['message' => 'Phone already verified'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$user || $user->verification_code !== $request->code) {
            return response()->json(
                ['message' => 'Invalid verification code'],
                Response::HTTP_UNPROCESSABLE_ENTITY
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
