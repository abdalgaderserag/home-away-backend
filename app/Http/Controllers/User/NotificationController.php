<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = Auth::user()->notifications();

        if ($request->has('unread') && $request->unread) {
            $query->unread();
        }

        $notifications = $query->paginate($request->perPage ? $request->perPage : 10);

        return response()->json([
            "messages" => $notifications->items(),
            "pagination" => [
                "current_page" => $notifications->currentPage(),
                "total_pages" => $notifications->lastPage(),
                "total_items" => $notifications->total(),
            ]
        ]);
    }



    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(["message" => "Notification marked as read"]);
        }

        return response()->json(["message" => "Notification not found"], Response::HTTP_NOT_FOUND);
    }
}
