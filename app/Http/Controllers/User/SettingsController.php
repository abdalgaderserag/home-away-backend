<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user->setting->email = $request->email;
        $user->setting->phone = $request->phone;
        $user->setting->update();
        return response($user->setting);
    }
}
