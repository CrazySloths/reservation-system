<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the authenticated user
     */
    public function getUnread()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        // Get recent notifications (both read and unread) from the database
        $notifications = DB::connection('auth_db')->table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get count of only unread notifications
        $unreadCount = DB::connection('auth_db')->table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();

        // Format notifications for display
        $formattedNotifications = $notifications->map(function ($notification) {
            $data = json_decode($notification->data, true);
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'message' => $data['message'] ?? 'New notification',
                'created_at' => $notification->created_at,
                'time_ago' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                'is_read' => $notification->read_at !== null,
                'data' => $data,
            ];
        });

        return response()->json([
            'notifications' => $formattedNotifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $updated = DB::connection('auth_db')->table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        DB::connection('auth_db')->table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get all notifications (read and unread)
     */
    public function index()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $notifications = DB::connection('auth_db')->table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Format notifications
        $notifications->getCollection()->transform(function ($notification) {
            $data = json_decode($notification->data, true);
            $notification->message = $data['message'] ?? 'New notification';
            $notification->time_ago = \Carbon\Carbon::parse($notification->created_at)->diffForHumans();
            $notification->data_array = $data;
            return $notification;
        });

        return view('notifications.index', compact('notifications'));
    }
}
