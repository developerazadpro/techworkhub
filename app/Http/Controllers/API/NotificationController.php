<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        return $request->user()
            ->notifications()
            ->latest()
            ->get();
    }

    public function markAsRead(Notification $notification)
    {
       $this->authorize('update', $notification);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
            $message = 'Marked as read';
        } else {
            $message = 'Already read';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'notification' => [
                'id' => $notification->id,
                'read_at' => $notification->read_at,
            ]
        ]);
    }
}
