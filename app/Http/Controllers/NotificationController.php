<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, string $notification)
    {
        $notif = $request->user()->notifications()->findOrFail($notification);
        $notif->markAsRead();

        return redirect($notif->data['url'] ?? back()->getTargetUrl());
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
