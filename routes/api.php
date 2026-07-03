<?php

use App\Http\Controllers\TelegramController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;

Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

// Dashboard data for charts (daily compliance)
Route::get('/dashboard/logs-stats', [DashboardController::class, 'stats']);

// PushButton confirmation endpoint (push button)
Route::match(['get', 'post'], '/device/confirm', [DeviceController::class, 'confirm']);