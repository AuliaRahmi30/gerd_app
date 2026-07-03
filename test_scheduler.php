<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JadwalMakan;
use Carbon\Carbon;

$now = now('Asia/Jakarta');
$inFiveMinutes = $now->copy()->addMinutes(5)->format('H:i:s');

$jadwal = JadwalMakan::create([
    'user_id' => 9,
    'jam' => $inFiveMinutes,
    'keterangan' => 'Test Notification - 5 minutes',
]);

echo "✅ Created test jadwal:\n";
echo "   ID: " . $jadwal->id . "\n";
echo "   Time: " . $inFiveMinutes . " (in 5 minutes from " . $now->format('H:i:s') . ")\n";
echo "   User: 9 (Aulia Rahmi)\n";
echo "\nScheduler will send notification when the minute matches.\n";
echo "Check logs: storage/logs/laravel.log\n";
