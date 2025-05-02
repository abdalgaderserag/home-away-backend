<?php

namespace App\Http\Controllers\User;

use App\Enum\Project\Status;
use App\Enum\User\UserType;
use App\Http\Controllers\Controller;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile($id = '')
    {
        // find the user if id is passed else get Auth user
        if ($id) {
            $user = User::find($id);
            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }
        } else {
            $user = Auth::user();
        }
        if ($user->type === UserType::Designer->value) {
            // get the project that is completed
            $projects = $user->designer_projects()->where('status', Status::Published->value)->count();
            $rates = Rate::where('designer_id', $user->id)->where('type', $user->type)->get();
            $rate_count = $rates->count();
            $rate_sum = $rates->sum('rate');
            $rate_avg = $rate_count > 0 ? $rate_sum / $rate_count : 0;
        } else {
            $projects = $user->client_projects()->where('status', Status::Published->value)->get();
            $rates = Rate::where('client_id', $user->id)->where('type', $user->type)->get();
            $rate_count = $rates->count();
            $rate_sum = $rates->sum('rate');
            $rate_avg = $rate_count > 0 ? $rate_sum / $rate_count : 0;
        }
        return response([
            'user' => $user,
            'projects' => $projects,
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

        if (!Hash::check($request->old_password, $user->password)) {
            return response(['message' => 'Old password is incorrect'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return response(['message' => 'Password changed successfully']);
    }
}
