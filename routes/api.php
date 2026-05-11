<?php

use App\Http\Controllers\Api\Mobile\AttendanceController;
use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:6,1');

    Route::middleware('mobile.auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('attendance', [AttendanceController::class, 'index']);
        Route::post('attendance/check-in', [AttendanceController::class, 'checkIn'])->middleware('throttle:10,1');
        Route::post('attendance/check-out', [AttendanceController::class, 'checkOut'])->middleware('throttle:10,1');
        Route::get('attendance/{attendance}', [AttendanceController::class, 'show']);

        Route::get('tasks', [TaskController::class, 'index']);
        Route::get('tasks/{task}', [TaskController::class, 'show']);
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    });
});
