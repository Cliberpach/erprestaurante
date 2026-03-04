<?php

use App\Http\Controllers\Tenant\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "alerts"], function () {
    Route::get('/get-all', [NotificationController::class, 'getNotifications'])->name('notifications.getAll');
    Route::get('/notifications/count', [NotificationController::class, 'getNotificationsCount'])->name('notifications.count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/notified', [NotificationController::class, 'notified'])->name('notifications.notified');
    Route::put('/notifications/finish/{id}', [NotificationController::class, 'finish'])->name('notifications.finish');
});
