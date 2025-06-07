<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Display a listing of the admin's notifications.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        
        return Inertia::render('Admin/Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }
    
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }
        
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read for the authenticated admin.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
