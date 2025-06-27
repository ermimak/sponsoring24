<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        
        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }
    
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        // return back to the same page
        return back();
        // return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        // return response()->json(['message' => 'All notifications marked as read']);
        return back();
    }
}
