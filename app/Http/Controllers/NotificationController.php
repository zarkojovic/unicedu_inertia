<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class NotificationController extends Controller {

    public function showNotifications() {
        $notifications = User::getUserNotifications();

        return Inertia::render('Student/Notifications',
            ['notifications' => $notifications]);
    }

    public function markAsRead() {
        auth()->user()->notifications()->update(['is_read' => 1]);
        return redirect()->back();
    }

}
