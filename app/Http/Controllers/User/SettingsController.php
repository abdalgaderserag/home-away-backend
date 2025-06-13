<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return response($user->setting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|boolean',
            'phone' => 'required|boolean'
        ]);
        $user = Auth::user();
        $not_set = '';
        if (empty($user->email)) {
            $not_set = 'email';
        } else {
            $user->setting->mail_notifications = $request->email;
        }
        if (empty($user->phone)) {
            $not_set = 'phone';
        } else {
            $user->setting->sms_notifications = $request->phone;
        }
        $user->setting->update();
        Cache::forget("user:{$user->id}:settings");

        if ($not_set === '') {
            return response(['settings' => $user->setting], Response::HTTP_OK);
        } else {
            return response(['settings' => $user->setting, 'not_set' => $not_set], Response::HTTP_BAD_REQUEST);
        }
    }
}
