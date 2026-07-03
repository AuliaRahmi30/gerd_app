<?php

namespace App\Console\Commands;

use App\Models\JadwalMakan;
use App\Models\LogMakan;
use App\Services\MqttService;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunMealScheduler extends Command
{
    protected $signature = 'meal:run-scheduler';
    protected $description = 'Jalankan meal scheduler setiap menit untuk pengingat dan MQTT';

    public function handle(): int
    {
        $this->info('Running meal scheduler...');

        try {
            $this->runOnce();
        } catch (\Throwable $exception) {
            Log::error('Meal scheduler error', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        return self::SUCCESS;
    }

    protected function runOnce(): void
    {
        $now = now(config('app.timezone'));

        $toleranceMinutes = 2;
        $windowStart = $now->copy()->subMinutes($toleranceMinutes)->format('H:i:s');
        $windowEnd = $now->format('H:i:s');
        $today = $now->toDateString();

        // =========================
        // AMBIL JADWAL
        // =========================
        $jadwals = JadwalMakan::with('user')
            ->where(function ($query) use ($windowStart, $windowEnd) {
                $query->whereRaw('TIME(jam) BETWEEN ? AND ?', [
                    $windowStart,
                    $windowEnd
                ]);
            })
            ->get();

        Log::info('Meal scheduler check', [
            'now' => $now->format('H:i:s'),
            'window_start' => $windowStart,
            'window_end' => $windowEnd,
            'match_count' => $jadwals->count(),
        ]);

        foreach ($jadwals as $jadwal) {

            $user = $jadwal->user;

            // =========================
            // 1. VALIDASI USER
            // =========================
            if (!$user || !$user->chat_id) {
                Log::warning('Missing chat_id', [
                    'jadwal_id' => $jadwal->id,
                ]);
                continue;
            }

            // =========================
            // 2. CEK APAKAH REMINDER SUDAH PERNAH DIKIRIM
            // =========================
            $log = $jadwal->logs()
                ->where('tanggal', $today)
                ->latest('id')
                ->first();

            if ($log && in_array($log->status, ['sudah', 'telat'])) {
                Log::info('Skip already processed', [
                    'jadwal_id' => $jadwal->id,
                    'status' => $log->status,
                ]);
                continue;
            }

            if ($log && $log->reminder_sent_at) {
                Log::info('Skip already reminded', [
                    'jadwal_id' => $jadwal->id,
                    'reminder_sent_at' => $log->reminder_sent_at->toDateTimeString(),
                ]);
                continue;
            }

            if (!$log) {
                $log = $jadwal->logs()->create([
                    'user_id' => $jadwal->user_id,
                    'jadwal_makan_id' => $jadwal->id,
                    'tanggal' => $today,
                    'jam' => $jadwal->jam,
                    'jadwal' => $jadwal->keterangan,
                    'status' => 'menunggu',
                    'konfirmasi' => null,
                ]);
            }

            // =========================
            // 3. KIRIM TELEGRAM (HANYA SEKALI)
            // =========================
            Log::info('Sending Telegram reminder', [
                'chat_id' => $user->chat_id,
                'jadwal_id' => $jadwal->id,
            ]);

            $response = TelegramService::sendScheduleReminder(
                $user->chat_id,
                $jadwal
            );

            $isSuccess = $response
                && method_exists($response, 'json')
                && ($response->json('ok') === true);

            if (!$isSuccess) {
                Log::error('Telegram FAILED', [
                    'chat_id' => $user->chat_id,
                    'jadwal_id' => $jadwal->id,
                    'response_body' => $response?->body(),
                ]);
            } else {
                $log->update(['reminder_sent_at' => now(config('app.timezone'))]);

                Log::info('Telegram SUCCESS', [
                    'chat_id' => $user->chat_id,
                    'jadwal_id' => $jadwal->id,
                ]);
            }

            // =========================
            // 4. MQTT
            // =========================
            try {
                $mqttService = app(MqttService::class);

                $scheduleTopic = config('mqtt.device_schedule_topic', 'gerd/jadwal');
                $schedulePayload = "{$jadwal->id}|" . Carbon::parse($jadwal->jam)->format('H:i:s');

                $mqttService->publish($scheduleTopic, $schedulePayload);

                $mqttService->publish(
                    config('mqtt.device_topic', 'gerd/buzzer'),
                    config('mqtt.device_message', 'ON')
                );

                Log::info('MQTT sent', [
                    'jadwal_id' => $jadwal->id,
                ]);
            } catch (\Throwable $exception) {
                Log::error('MQTT FAILED', [
                    'error' => $exception->getMessage(),
                    'jadwal_id' => $jadwal->id,
                ]);
            }
        }
    }
}