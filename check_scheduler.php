<?php

require 'bootstrap/app.php';
$app = require_once 'bootstrap/app.php';

use App\Models\JadwalMakan;
use App\Models\User;
use App\Models\LogMakan;
use Illuminate\Support\Facades\DB;

// Set up the app
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USERS ===\n";
foreach (User::all() as $u) {
    echo sprintf("%d: %s (chat_id: %s)\n", $u->id, $u->name, $u->chat_id ?? 'NULL');
}

echo "\n=== JADWAL MAKAN ===\n";
foreach (JadwalMakan::all() as $j) {
    echo sprintf("%d: %s - %s (user_id: %d)\n", $j->id, $j->jam, $j->keterangan, $j->user_id);
}

echo "\n=== LOG MAKAN TODAY ===\n";
$today = now('Asia/Jakarta')->toDateString();
foreach (LogMakan::where('tanggal', $today)->orderBy('created_at', 'desc')->limit(10)->get() as $l) {
    echo sprintf("Log %d: jadwal_id=%d status=%s jam=%s tanggal=%s\n", 
        $l->id, $l->jadwal_makan_id, $l->status, $l->jam, $l->tanggal);
}

echo "\n=== CURRENT TIME ===\n";
echo "Now: " . now('Asia/Jakarta')->format('Y-m-d H:i:s') . "\n";
echo "Timezone: " . config('app.timezone') . "\n";
