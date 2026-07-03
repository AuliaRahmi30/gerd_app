<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\JadwalMakan;
use App\Services\MqttService;
use App\Services\TelegramService;
use App\Services\MealLogService;
use Carbon\Carbon;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Exceptions\MqttClientException;

class MqttListener extends Command
{
    protected $signature = 'mqtt:listen';

    protected $description = 'Mendengarkan MQTT';

    public function handle()
    {
        $mqttService = app(MqttService::class);

        try {
            $mqtt = $mqttService->createClient();
            $this->registerSubscriptions($mqtt, $mqttService);
            $this->info('Terhubung ke broker MQTT di ' . config('mqtt.host') . ':' . config('mqtt.port'));
            $mqtt->loop(true);
        } catch (MqttClientException $exception) {
            $this->error('MQTT connection failed: ' . $exception->getMessage());
            Log::error('Koneksi listener MQTT gagal', [
                'error' => $exception->getMessage(),
                'host' => config('mqtt.host'),
                'port' => config('mqtt.port'),
            ]);

            return 1;
        }

        return 0;
    }

    protected function registerSubscriptions(MqttClient $mqtt, MqttService $mqttService): void
    {
        $mqtt->subscribe(
            config('mqtt.device_status_topic', 'gerd/status'),
            function (string $topic, string $message) use ($mqttService) {
                $mqttService->markDeviceSeen();
                Log::info('Menerima heartbeat perangkat', ['topic' => $topic, 'payload' => trim($message)]);
            },
            0
        );

        $mqtt->subscribe(
            config('mqtt.device_confirm_topic', 'gerd/konfirmasi'),
            function (string $topic, string $message) use ($mqttService) {
                $this->handleDeviceConfirmationMessage($topic, $message);
            },
            0
        );

        $mqtt->subscribe(
            config('mqtt.device_late_topic', 'gerd/late'),
            function (string $topic, string $message) use ($mqttService) {
                $this->handleDeviceLateMessage($topic, $message);
            },
            0
        );
    }

    protected function handleDeviceConfirmationMessage(string $topic, string $message): void
    {
        $payload = trim($message);
        [$status, $jadwalId, $scheduledTime] = $this->parseConfirmationPayload($payload);

        if ($status === '' && ! $jadwalId && ! $scheduledTime) {
            Log::warning('MqttListener: Payload konfirmasi MQTT diabaikan', ['topic' => $topic, 'payload' => $payload]);
            return;
        }

        Log::info('MqttListener: Pesan MQTT diterima', [
            'topic' => $topic,
            'payload' => $payload,
            'status' => $status,
            'jadwal_id' => $jadwalId,
            'scheduled_time' => $scheduledTime,
        ]);

        $confirmTime = $this->parseScheduledTime($scheduledTime) ?? Carbon::now()->format('H:i:s');
        $jadwal = $this->findJadwalByIdOrTime($jadwalId, $scheduledTime, true);

        if (! $jadwal) {
            Log::warning('MqttListener: Tidak ditemukan jadwal_makan untuk konfirmasi MQTT', ['payload' => $payload]);
            return;
        }

        app(MealLogService::class)->recordMealConfirmation($jadwal->user_id, $jadwal->id, 'device', $confirmTime);

        Log::info('MqttListener: Konfirmasi makan tercatat', [
            'user_id' => $jadwal->user_id,
            'jadwal_id' => $jadwal->id,
            'keterangan' => $jadwal->keterangan,
            'confirm_time' => $confirmTime,
        ]);

        if ($jadwal->user && $jadwal->user->chat_id) {
            try {
                TelegramService::sendConfirmationReceived($jadwal->user->chat_id, $jadwal->keterangan);
                Log::info('MqttListener: Telegram konfirmasi terkirim', [
                    'chat_id' => $jadwal->user->chat_id,
                    'jadwal_id' => $jadwal->id,
                ]);
            } catch (\Exception $e) {
                Log::error('MqttListener: Gagal mengirim konfirmasi Telegram', [
                    'chat_id' => $jadwal->user->chat_id,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::warning('MqttListener: Tidak dapat mengirim Telegram - chat_id hilang', ['user_id' => $jadwal->user_id]);
        }

        $this->info('✅ MQTT confirmation processed for jadwal_id=' . $jadwal->id);
    }

    protected function handleDeviceLateMessage(string $topic, string $message): void
    {
        $payload = trim($message);
        [$status, $jadwalId, $scheduledTime] = $this->parseLatePayload($payload);

        Log::info('MqttListener: Pesan MQTT telat diterima', [
            'topic' => $topic,
            'payload' => $payload,
            'status' => $status,
        ]);

        if ($status !== 'LATE') {
            Log::warning('MqttListener: Payload MQTT telat diabaikan', ['topic' => $topic, 'payload' => $payload]);
            return;
        }

        $jadwal = $this->findJadwalByIdOrTime($jadwalId, $scheduledTime, false);

        if (! $jadwal) {
            Log::warning('MqttListener: Tidak ditemukan jadwal_makan untuk notifikasi telat MQTT', ['payload' => $payload]);
            return;
        }

        Log::info('MqttListener: Perangkat melaporkan telat untuk jadwal', [
            'jadwal_id' => $jadwal->id,
            'keterangan' => $jadwal->keterangan,
            'user_id' => $jadwal->user_id,
        ]);

        $this->info('⚠️ Sinyal telat MQTT diterima untuk jadwal_id=' . $jadwal->id);
    }

    protected function parseConfirmationPayload(string $payload): array
    {
        $parts = array_map('trim', explode('|', $payload));
        $firstPart = $parts[0] ?? '';

        if ($firstPart === '') {
            return ['', null, null];
        }

        if (is_numeric($firstPart) && count($parts) === 1) {
            return ['DONE', (int) $firstPart, null];
        }

        $status = strtoupper($firstPart);
        $jadwalId = null;
        $scheduledTime = null;

        if (! in_array($status, ['DONE', 'OK', 'CONFIRMED'], true)) {
            return ['', null, null];
        }

        if (isset($parts[1]) && is_numeric($parts[1])) {
            $jadwalId = (int) $parts[1];
        } elseif (isset($parts[1]) && $parts[1] !== '') {
            $scheduledTime = $parts[1];
        }

        return [$status, $jadwalId, $scheduledTime];
    }

    protected function parseLatePayload(string $payload): array
    {
        $parts = array_map('trim', explode('|', $payload));
        $status = strtoupper($parts[0] ?? '');
        $jadwalId = null;
        $scheduledTime = null;

        if ($status !== 'LATE') {
            return [$status, null, null];
        }

        if (isset($parts[1]) && is_numeric($parts[1])) {
            $jadwalId = (int) $parts[1];
        } elseif (isset($parts[1]) && $parts[1] !== '') {
            $scheduledTime = $parts[1];
        }

        return [$status, $jadwalId, $scheduledTime];
    }

    protected function parseScheduledTime(?string $scheduledTime): ?string
    {
        if (! $scheduledTime) {
            return null;
        }

        try {
            return Carbon::parse($scheduledTime)->format('H:i:s');
        } catch (\Exception $e) {
            Log::warning('MqttListener: Tidak dapat mengurai waktu terjadwal dari payload MQTT', [
                'scheduled_time' => $scheduledTime,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    protected function findJadwalByIdOrTime(?int $jadwalId, ?string $scheduledTime, bool $useClosestPast = false): ?JadwalMakan
    {
        if ($jadwalId) {
            $jadwal = JadwalMakan::find($jadwalId);
            Log::info('MqttListener: Searching jadwal by ID', ['jadwal_id' => $jadwalId, 'found' => $jadwal ? 'yes' : 'no']);
            if ($jadwal) {
                return $jadwal;
            }
        }

        if (! $scheduledTime) {
            return null;
        }

        $time = $this->parseScheduledTime($scheduledTime);
        if ($time) {
            $jadwal = JadwalMakan::whereRaw('TIME(jam) = ?', [$time])->first();
            Log::info('MqttListener: Mencari jadwal berdasarkan waktu terjadwal', ['scheduled_time' => $time, 'found' => $jadwal ? 'yes' : 'no']);
            if ($jadwal) {
                return $jadwal;
            }
        }

        if (! $useClosestPast) {
            return null;
        }

        $currentTime = Carbon::now()->format('H:i:s');
        $jadwal = JadwalMakan::whereRaw('TIME(jam) <= ?', [$currentTime])
            ->orderBy('jam', 'desc')
            ->first();

        Log::info('MqttListener: Searching jadwal by current time', ['current_time' => $currentTime, 'found' => $jadwal ? 'yes' : 'no']);

        return $jadwal;
    }
}
