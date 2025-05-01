<?php

namespace App\Http\Controllers;

use App\Enum\VerificationType;
use App\Http\Requests\StoreVerificationRequest;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Verification::where('user_id', Auth::id())
            ->where('type', VerificationType::User->value)->first();
        $company = Verification::where('user_id', Auth::id())
            ->where('type', VerificationType::Company->value)->first();
        $address  = Verification::where('user_id', Auth::id())
            ->where('type', VerificationType::Address->value)->first();

        return response([
            'user_verification' => $user,
            'company_verification' => $company,
            'address_verification' => $address
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVerificationRequest $request)
    {
        $verification = Verification::where('user_id', Auth::id())->where('type', $request->type)->first();
        if ($verification->verified) {
            return response("you have already verified your " . $request->type, Response::HTTP_NOT_MODIFIED);
        } elseif ($verification) {
            $verification->user_id = Auth::id();
            $verification->attachments = $request->attachments;
            $verification->update();
            return response($verification, Response::HTTP_OK);
        }
        $verification = new Verification();
        $verification->user_id = Auth::id();
        $verification->attachments = $request->attachments;
        $verification->verified = false;
        $verification->save();
        return response($verification, Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(verification $verification)
    {
        if ($verification->user_id == Auth::id()) {
            $verification->delete();
            return response()->noContent();
        }
        return response("you are not the ID owner", Response::HTTP_UNAUTHORIZED);
    }
}
