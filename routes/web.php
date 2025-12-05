<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Models\Notification;
use App\Events\NewNotificationEvent;
use App\Models\User;


Route::get('/test-notification', function () {
    $notification = Notification::create([
        'user_id' => 1,
        'message' => 'This is a live test notification!',
        'type' => 13
    ]);
    broadcast(new NewNotificationEvent($notification))->toOthers();
    return 'Test notification sent!';
});

