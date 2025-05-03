<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $notifications = Auth::user()->notifications;
        return response()->json(["messages" => $notifications]);
    }
}
