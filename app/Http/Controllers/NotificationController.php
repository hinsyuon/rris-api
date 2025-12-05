<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    //
    public function index(Request $request)
    {
        return Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function unread(Request $request)
    {
        return Notification::where('user_id', $request->user()->id)
            ->where('read_status', Notification::STATUS_UNREAD)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read_status' => Notification::STATUS_READ]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return response()->json(['message' => 'Notification deleted']);
    }
}
