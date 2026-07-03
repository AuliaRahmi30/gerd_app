<?php

use App\Models\JadwalMakan;
use App\Models\LogMakan;
use App\Models\User;
use App\Services\MqttService;
use Illuminate\Support\Facades\Http;

test('scheduler sends reminder once for a schedule that already has a waiting log for today', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $this->app->instance(MqttService::class, new class extends MqttService {
        public function publish($topic, $message): void
        {
            // no-op for tests
        }
    });

    $user = User::factory()->create([
        'chat_id' => '123456789',
    ]);

    $jadwal = JadwalMakan::create([
        'user_id' => $user->id,
        'jam' => now()->subMinute()->format('H:i:s'),
        'keterangan' => 'Sarapan',
    ]);

    LogMakan::create([
        'user_id' => $user->id,
        'jadwal_makan_id' => $jadwal->id,
        'tanggal' => now()->toDateString(),
        'jam' => $jadwal->jam,
        'jadwal' => $jadwal->keterangan,
        'status' => LogMakan::STATUS_WAITING,
        'konfirmasi' => null,
    ]);

    $this->artisan('meal:run-scheduler')->assertExitCode(0);

    Http::assertSentCount(1);

    $log = LogMakan::where('jadwal_makan_id', $jadwal->id)
        ->where('tanggal', now()->toDateString())
        ->first();

    expect($log)->not->toBeNull();
    expect($log->reminder_sent_at)->not->toBeNull();
});
