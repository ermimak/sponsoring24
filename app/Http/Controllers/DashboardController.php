<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard index page.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get latest 10 notifications for the user
        $notifications = $user->notifications()->latest()->limit(10)->get();
        
        // Count unread notifications
        $unreadCount = $user->unreadNotifications()->count();

        return Inertia::render('Dashboard/Index', [
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadCount,
        ]);
    }
}
