<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    /**
     * Send a message to Telegram chat
     *
     * @param mixed $chatId
     * @param string $text
     * @param string|null $parseMode
     * @return \Illuminate\Http\Client\Response|bool
     */
    public static function send($chatId, $text, $parseMode = null)
    {
        // Validasi chat_id
        if (empty($chatId)) {
            \Log::warning('Telegram Send skipped: chat_id is empty');
            return false;
        }

        $token = config('services.telegram.bot_token');
        
        // Validasi token
        if (empty($token)) {
            \Log::error('Telegram Send failed: bot_token not configured');
            return false;
        }

        $maxRetries = 3;
        $retryDelay = 1; // detik

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($parseMode) {
            $payload['parse_mode'] = $parseMode;
        }

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout(30)
                    ->connectTimeout(10)
                    ->post("https://api.telegram.org/bot{$token}/sendMessage", $payload);

                if ($response->successful() && ($response->json('ok') ?? false)) {
                    \Log::info('Telegram Send Success', [
                        'chat_id' => $chatId,
                        'attempt' => $attempt,
                    ]);
                    return $response;
                }

                $status = $response->status();
                $body = $response->body();

                \Log::warning('Telegram Send failed', [
                    'chat_id' => $chatId,
                    'payload' => $payload,
                    'status' => $status,
                    'body' => $body,
                    'attempt' => $attempt,
                ]);

                if (($status >= 500 || $status === 429) && $attempt < $maxRetries) {
                    sleep($retryDelay);
                    continue;
                }

                return $response;
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                $transient = self::isTransientTelegramError($errorMsg);

                \Log::warning('Telegram Send Exception (attempt ' . $attempt . '): ' . $errorMsg, [
                    'chat_id' => $chatId,
                    'error' => $errorMsg,
                    'attempt' => $attempt,
                    'transient' => $transient,
                ]);

                if ($transient && $attempt < $maxRetries) {
                    sleep($retryDelay);
                    continue;
                }

                if ($attempt >= $maxRetries) {
                    \Log::error('Telegram Send Error (max retries reached): ' . $errorMsg, [
                        'chat_id' => $chatId,
                        'payload' => $payload,
                        'total_attempts' => $maxRetries,
                    ]);
                }

                return false;
            }
        }

        return false;
    }

    /**
     * Check if error is transient (can be retried)
     */
    protected static function isTransientTelegramError(string $errorMsg): bool
    {
        $errorMsg = strtolower($errorMsg);

        $transientPatterns = [
            'timeout',
            'could not resolve host',
            'failed to connect',
            'connection reset',
            'temporary failure',
            'dns',
            'connection refused',
            'network is unreachable',
        ];

        foreach ($transientPatterns as $pattern) {
            if (str_contains($errorMsg, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Send schedule created notification
     */
    public static function sendScheduleCreated($chatId, $jam, $keterangan)
    {
        $message = "✅ <b>Jadwal Makan Ditambahkan</b>\n\n";
        $message .= "⏰ <b>Waktu:</b> {$jam}\n";
        $message .= "🍴 <b>Jenis:</b> {$keterangan}\n\n";
        $message .= "Jadwal makan Anda telah tersimpan. Saya akan mengingatkan Anda pada waktunya!";

        return self::send($chatId, $message, 'HTML');
    }

    /**
     * Send schedule updated notification
     */
    public static function sendScheduleUpdated($chatId, $jam, $keterangan)
    {
        $message = "✏️ <b>Jadwal Makan Diperbarui</b>\n\n";
        $message .= "⏰ <b>Waktu:</b> {$jam}\n";
        $message .= "🍴 <b>Jenis:</b> {$keterangan}\n\n";
        $message .= "Jadwal makan Anda telah diperbarui.";

        return self::send($chatId, $message, 'HTML');
    }

    /**
     * Send schedule deleted notification
     */
    public static function sendScheduleDeleted($chatId, $jam, $keterangan)
    {
        $message = "🗑️ <b>Jadwal Makan Dihapus</b>\n\n";
        $message .= "⏰ <b>Waktu:</b> {$jam}\n";
        $message .= "🍴 <b>Jenis:</b> {$keterangan}\n\n";
        $message .= "Jadwal makan ini telah dihapus dari pengingat Anda.";

        return self::send($chatId, $message, 'HTML');
    }

    /**
     * Send schedule reminder notification
     */
    public static function sendScheduleReminder($chatId, $jadwal)
    {
        $message = "🔔 <b>PENGINGAT JADWAL MAKAN</b>\n\n";
        $message .= "🍽️ <b>Jadwal:</b> {$jadwal->keterangan}\n";
        $message .= "⏰ <b>Jam:</b> {$jadwal->jam}\n\n";
        $message .= "⏱ <b>Batas Konfirmasi: 10 menit</b>\n";
        $message .= "Tekan tombol pada perangkat atau balas <b>sudah</b> di sini.";

        return self::send($chatId, $message, 'HTML');
    }

    /**
     * Send late notification
     */
    public static function sendLateNotification($chatId, $jadwal)
    {
        $message = "⏰ <b>BATAS WAKTU TERLAMPAUI</b>\n\n";
        $message .= "🍽️ <b>Jadwal:</b> {$jadwal->keterangan}\n";
        $message .= "⏰ <b>Jam:</b> {$jadwal->jam}\n";
        $message .= "⚠️ <b>Status:</b> TELAT MAKAN\n\n";
        $message .= "Toleransi 10 menit sudah habis.";

        return self::send($chatId, $message, 'HTML');
    }

    /**
     * Send confirmation received notification
     * Dipanggil dari MqttListener setelah menerima konfirmasi dari perangkat
     */
    public static function sendConfirmationReceived($chatId, $keterangan)
    {
        $message = "✅ <b>Konfirmasi Makan Diterima</b>\n\n";
        $message .= "🍽️ <b>Jadwal:</b> {$keterangan}\n";
        $message .= "⏰ <b>Waktu:</b> " . now()->format('H:i:s') . "\n\n";
        $message .= "Terima kasih telah mengonfirmasi!";

        return self::send($chatId, $message, 'HTML');
    }
}