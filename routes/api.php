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
Route::post('/room-types/bulk-delete', [RoomTypeController::class, 'bulk_delete']);

# Rooms
Route::get('/rooms', [App\Http\Controllers\RoomController::class, 'index']);
Route::get('/rooms/{id}', [App\Http\Controllers\RoomController::class, 'show']);
Route::post('/rooms', [App\Http\Controllers\RoomController::class, 'store']);
Route::put('/rooms/{id}', [App\Http\Controllers\RoomController::class, 'update']);
Route::delete('/rooms/{id}', [App\Http\Controllers\RoomController::class, 'destroy']);
Route::post('/rooms/bulk-delete', [App\Http\Controllers\RoomController::class, 'bulk_delete']);
# Tenants
Route::get('/tenants', [App\Http\Controllers\TenantController::class, 'index']);
Route::get('/tenants/{id}', [App\Http\Controllers\TenantController::class, 'show']);
Route::post('/tenants', [App\Http\Controllers\TenantController::class, 'store']);
Route::put('/tenants/{id}', [App\Http\Controllers\TenantController::class, 'update']);
Route::delete('/tenants/{id}', [App\Http\Controllers\TenantController::class, 'destroy']); 
Route::post('/tenants/bulk-delete', [App\Http\Controllers\TenantController::class, 'bulk_delete']);



