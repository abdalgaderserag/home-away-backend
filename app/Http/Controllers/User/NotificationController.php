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
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();

        if ($request->has('unread') && $request->unread) {
            $query->unread();
        }

        if ($request->has('type') && $request->type) {
            $query->whereJsonContains('data->type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')
                              ->paginate($request->perPage ? $request->perPage : 10);

        return response()->json([
            "messages" => $notifications->items(),
            "pagination" => [
                "current_page" => $notifications->currentPage(),
                "total_pages" => $notifications->lastPage(),
                "total_items" => $notifications->total(),
            ]
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                "message" => "Notification marked as read",
                "notification" => $notification
            ]);
        }

        return response()->json(["message" => "Notification not found"], Response::HTTP_NOT_FOUND);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json([
            "message" => "All notifications marked as read"
        ]);
    }

    /**
     * Get unread notification count.
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json([
            "unread_count" => $count
        ]);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->delete();
            return response()->json([
                "message" => "Notification has been deleted"
            ]);
        }
        
        return response()->json([
            "message" => "Notification not found"
        ], Response::HTTP_NOT_FOUND);
    }
}
