<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomTypeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

# Room Types
Route::get('/room-types', [RoomTypeController::class, 'index']);
Route::get('/room-types/{id}', [RoomTypeController::class, 'find']);
Route::post('/room-types', [RoomTypeController::class, 'store']);
Route::put('/room-types/{id}', [RoomTypeController::class, 'update']);
Route::delete('/room-types/{id}', [RoomTypeController::class, 'destroy']);

# Rooms
Route::get('/rooms', [App\Http\Controllers\RoomController::class, 'index']);



