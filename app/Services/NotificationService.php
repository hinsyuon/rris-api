<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NewNotificationEvent;


class NotificationService
{
    public static function send($userId, $message, $type)
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'message' => $message,
            'type' => $type,
            'read_status' => Notification::STATUS_UNREAD,
        ]);

        broadcast(new NewNotificationEvent($notification))->toOthers();

        return $notification;
    }
}
