<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JadwalMakanController;
use App\Http\Controllers\LogMakanController;
use App\Http\Controllers\DeviceController;
use App\Services\TelegramService;
use App\Models\User;
use App\Http\Controllers\DashboardController;

/*
| HOME
*/
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
| DASHBOARD
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard stats endpoint (used by the dashboard view, requires session auth)
Route::get('/dashboard/logs-stats', [DashboardController::class, 'stats'])->middleware('auth');

// Dashboard MQTT status endpoint
Route::get('/dashboard/mqtt-status', [DashboardController::class, 'mqttStatus'])->middleware('auth');

// Device confirmation endpoint alias for direct device callbacks
Route::match(['get', 'post'], '/device/confirm', [DeviceController::class, 'confirm']);

/*
| AUTH
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('jadwal-makan', JadwalMakanController::class);
    Route::resource('log-makan', LogMakanController::class);
});

/*
| TEST TELEGRAM
*/
Route::get('/test-telegram', function () {

    $user = User::first();

    if (!$user || !$user->chat_id) {
        return "❌ User atau chat_id masih kosong";
    }

    TelegramService::send(
        $user->chat_id,
        "🍽 TEST NOTIF"
    );

    return "✅ Notif terkirim";
});

require __DIR__.'/auth.php';