<?php

namespace App\Http\Controllers;

use App\Enum\VerificationType;
use App\Http\Requests\StoreVerificationRequest;
use App\Models\Attachment;
use App\Models\Verification;
use Coderflex\LaravelTicket\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        // Check if a verification of the given type already exists
        $verification = Verification::where('user_id', Auth::id())
            ->where('type', $request->type)
            ->first();

        if ($verification) {
            // If the verification exists and is already verified, return a message
            if ($verification->verified) {
                return response("you have already verified your " . $request->type, Response::HTTP_NOT_MODIFIED);
            }
            // If it's not verified yet, update the verification with new data
            foreach ($request->attachments as $attach) {
                $attachment = Attachment::find($attach);
                $attachment->verification_id = $verification->id;
                $attachment->save();
            }
            $verification->update();
            $this->createVerificationTicket($verification);
            return response($verification, Response::HTTP_OK);
        } else {

            // If no verification exists, create a new one
            $verification = new Verification();
            $verification->user_id = Auth::id();
            $verification->verified = false;
            $verification->type = $request->type;
            $verification->save();
            $this->createVerificationTicket($verification);
        }

        // Check if all verifications are completed and assign permissions
        $this->checkVerification();

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
        $this->checkVerification();
        return response("you are not the ID owner", Response::HTTP_UNAUTHORIZED);
    }

    private function createVerificationTicket(Verification $verification)
    {
        $user = Auth::user();
        $category = Category::where('slug', "{$type}-verification")->first();

        $user->tickets()->create([
            'title' => "{$verification->type} Verification request",
            'model_id' => $verification->id,
            'category_id' => $category->id,
            'status' => 'open',
            'priority' => 'medium',
        ]);
    }

    private function checkVerification()
    {
        $auth = Auth::user();
        $user = Verification::where('user_id', $auth->id)
            ->where('type', VerificationType::User->value)->first();
        $company = Verification::where('user_id', $auth->id)
            ->where('type', VerificationType::Company->value)->first();
        $address  = Verification::where('user_id', $auth->id)
            ->where('type', VerificationType::Address->value)->first();
        if ($user->verified && $company->verified && $address->verified) {
            $auth->givePermissionTo('verified user');
        } else if ($auth->hasPermissionTo('verified user')) {
            $auth->revokePermissionTo('verified user');
        }
    }
}
