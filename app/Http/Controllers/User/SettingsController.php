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
        return response($user->settings);
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
            $settings->mail_notifications = $request->mail_notifications;
        }
        
        // Check if phone is set before allowing SMS notifications
        if ($request->sms_notifications && empty($user->phone)) {
            $notSet[] = 'phone';
        } else {
            $settings->sms_notifications = $request->sms_notifications;
        }
        
        // Update language if provided
        if ($request->has('lang')) {
            $settings->lang = $request->lang;
        }
        
        $settings->save();
        
        // Clear the cache
        Cache::forget("user:{$user->id}:settings");

        if (empty($notSet)) {
            return response(['settings' => $settings], Response::HTTP_OK);
        } else {
            return response([
                'settings' => $settings, 
                'not_set' => $notSet
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
