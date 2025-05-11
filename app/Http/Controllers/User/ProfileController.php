<?php

namespace App\Http\Controllers\User;

use App\Enum\Project\Status;
use App\Enum\User\UserType;
use App\Enum\VerificationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateBioRequest;
use App\Models\Attachment;
use App\Models\Rate;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function profile($id = '')
    {
        // Find the user if id is passed, else get Auth user
        $user = $id ? User::find($id) : Auth::user();

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        // Common status for project filtering
        $publishedStatus = Status::Published->value;

        if ($user->type === UserType::Designer->value) {
            // Get completed projects
            $projects = $user->designer_projects()->where('status', $publishedStatus)->count();
            $rates = Rate::where('designer_id', $user->id)->where('type', $user->type)->get();
        } else {
            // Get published projects for client
            $projects = $user->client_projects()->where('status', $publishedStatus)->count();
            $rates = Rate::where('client_id', $user->id)->where('type', $user->type)->get();
        }

        // Calculate rate average
        $rate_count = $rates->count();
        $rate_sum = $rates->sum('rate');
        $rate_avg = $rate_count > 0 ? $rate_sum / $rate_count : 0;

        return response([
            'user' => $user,
            'projects_count' => $projects,
            'rates' => [
                'count' => $rate_count,
                'avg' => $rate_avg
            ]
        ]);
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if the old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return response(['message' => 'Old password is incorrect'], 400);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response(['message' => 'Password changed successfully'], Response::HTTP_OK);
    }


    public function updateBio(UpdateBioRequest $request)
    {
        // Retrieve the user's bio
        $bio = Auth::user()->bio;

        // Update bio details
        $bio->about = $request->about;
        $bio->price_per_meter = $request->price_per_meter;
        $bio->locations = $request->locations;
        $bio->save();

        return response()->json([
            'message' => 'Bio updated successfully',
            'bio' => $bio
        ], Response::HTTP_OK);
    }

    public function changeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);
        $user = Auth::user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->update();
        return response([
            'message' => 'Email updated successfully',
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function changePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|unique:users,phone,' . Auth::id(),
        ]);
        $user = Auth::user();
        $user->phone = $request->phone;
        $user->phone_verified_at = null;
        $user->update();
        return response([
            'message' => 'Phone updated successfully',
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function changeAvatar(Request $request)
    {
        $request->validate([
            'attachment' => 'required|exists:attachments,id',
        ]);

        $user = Auth::user();
        $attachment = Attachment::find($request->attachment);

        $attachment->user_id = $user->id;
        $path = $attachment->url;
        Storage::setVisibility($path, 'public');
        $attachment->save();
        $user->touch();

        return response()->json([
            'message' => 'Avatar updated successfully',
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function changeName(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->update();
        $verifications = Verification::where('user_id', $user->id);
        foreach ($verifications as $verification) {
            if ($verification->type == VerificationType::User || $verification->type == VerificationType::Address) {
                $verification->verified = false;
                $verification->update();
            }
        }
    }
}
