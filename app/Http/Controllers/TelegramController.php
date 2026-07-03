<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\JadwalMakan;
use App\Models\LogMakan;
use App\Services\TelegramService;
use App\Services\MqttService;
use App\Services\MealLogService;

class TelegramController extends Controller
{
    public function __construct(
        protected MealLogService $mealLogService,
    ) {}

    public function webhook(Request $request)
    {
        \Log::info('Telegram Webhook:', $request->all());

        $chatId = $request->input('message.chat.id');
        $text = $request->input('message.text');
        $firstName = $request->input('message.from.first_name');

        if (!$chatId) {
            return response()->json(['ok' => true]);
        }

        $user = User::where('chat_id', $chatId)->first();
        $message = "Halo {$firstName}! Saya adalah asisten pengingat makan Anda. ";

        // ============================================================
        // USER BELUM TERDAFTAR
        // ============================================================
        if (!$user) {
            if ($text && trim(strtolower($text)) === '/start') {
                $message = "Halo {$firstName}! Untuk menghubungkan akun Telegram dengan website GERDCare, silakan salin <b>Chat ID</b> berikut dan masukkan pada halaman Profil Website:\n\n";
                $message .= "<code>{$chatId}</code>\n\n";
                $message .= "Setelah itu, Anda akan menerima pengingat jadwal makan secara otomatis.";
            } else {
                $message .= "Akun Telegram Anda belum terhubung dengan akun website GERDCare. ";
                $message .= "Silakan kirim perintah <b>/start</b> untuk mendapatkan petunjuk lebih lanjut.\n\n";
                $message .= "Chat ID Anda: <code>{$chatId}</code>";
            }

            $this->sendTelegramMessage($chatId, $message);
            return response()->json(['ok' => true]);
        }

        // ============================================================
        // USER SUDAH TERDAFTAR
        // ============================================================
        if ($text) {
            $command = trim(strtolower($text));

            // ---------- /start ----------
            if ($command === '/start') {
                $message = "✅ Akun Telegram Anda telah berhasil terhubung dengan akun website GERDCare.\n\n";

                $jadwals = $user->jadwals()->orderBy('jam')->get();

                if ($jadwals->isNotEmpty()) {
                    $message .= "📋 <b>Jadwal Makan Anda:</b>\n";
                    foreach ($jadwals as $jadwal) {
                        $message .= "• {$jadwal->jam} - {$jadwal->keterangan}\n";
                    }
                } else {
                    $message .= "⚠️ Belum ada jadwal makan. Silakan atur jadwal di website GERDCare.\n\n";
                }

                $message .= "\n💡 <b>Perintah yang tersedia:</b>\n";
                $message .= "/start - Lihat status akun\n";
                $message .= "/jadwal - Lihat daftar jadwal\n";
                $message .= "/help - Bantuan\n";

            // ---------- /jadwal ----------
            } elseif ($command === '/jadwal') {
                $jadwals = $user->jadwals()->orderBy('jam')->get();

                if ($jadwals->isNotEmpty()) {
                    $message = "📋 <b>Daftar Jadwal Makan Anda:</b>\n\n";
                    foreach ($jadwals as $jadwal) {
                        $message .= "• {$jadwal->jam} - {$jadwal->keterangan}\n";
                    }
                } else {
                    $message = "⚠️ Belum terdapat jadwal makan. Silakan atur jadwal di website GERDCare terlebih dahulu.";
                }

            // ---------- /help ----------
            } elseif ($command === '/help' || $command === 'help') {
                $message = "📖 <b>Panduan Penggunaan Bot GERDCare</b>\n\n";
                $message .= "/start - Lihat status akun dan jadwal\n";
                $message .= "/jadwal - Lihat daftar jadwal makan\n";
                $message .= "/help - Tampilkan panduan ini\n\n";
                $message .= "🔔 Bot akan mengirimkan pengingat saat jadwal makan tiba.\n\n";
                $message .= "Setelah menerima pengingat, konfirmasi utama tetap melalui tombol Push Button.\n";
                $message .= "Telegram hanya digunakan untuk notifikasi pengingat dan status akun.";

            // ---------- "sudah" ----------
            } elseif ($command === 'sudah') {
                $shouldSend = true;
                $message = "Terima kasih atas konfirmasinya.";

                try {
                    $latestLog = $this->mealLogService->getLatestConfirmationForToday($user->id);
                    $latestJadwal = $latestLog?->jadwalMakan ?? $this->mealLogService->getLatestScheduleForToday($user->id);

                    if (!$latestJadwal) {
                        $message = "Maaf, saya tidak menemukan jadwal terbaru untuk dikonfirmasi.\n\n";
                        $message .= "Silakan kirim /start untuk melihat status akun Anda.";
                        $shouldSend = true;
                    } else {
                        if ($latestLog && $latestLog->status === 'telat') {
                            $message = "⚠️ Konfirmasi Anda diterima, tetapi status sudah <b>TELAT</b>.";
                        } else {
                            $message = "✅ Konfirmasi makan <b>{$latestJadwal->keterangan}</b> diterima!\n";
                            $message .= "⏰ Waktu: " . now()->format('H:i:s');
                        }

                        $this->mealLogService->recordMealConfirmation(
                            $user->id,
                            $latestJadwal->id,
                            'telegram'
                        );

                        app(MqttService::class)->publish(
                            config('mqtt.device_topic', 'gerd/buzzer'),
                            config('mqtt.device_stop_message', 'OFF')
                        );
                    }
                } catch (\Throwable $exception) {
                    \Log::error('Failed to process Telegram confirmation', [
                        'error' => $exception->getMessage(),
                        'user_id' => $user->id,
                    ]);
                    $message = "⚠️ Terjadi kesalahan saat memproses konfirmasi. Silakan coba lagi.";
                }

                if ($shouldSend && $message) {
                    $message .= "\n\n💡 Kirim /start untuk melihat status akun.";
                }

            // ---------- Unknown Command ----------
            } else {
                $message = "Maaf, saya tidak mengenali perintah \"{$text}\".\n\n";
                $message .= "Kirim /help untuk melihat daftar perintah yang tersedia.";
            }

        } else {
            // Tidak ada teks
            $message = "Halo {$firstName}! Silakan kirim /start untuk melihat petunjuk dan status akun Anda.";
        }

        // Kirim pesan jika perlu
        if (!isset($shouldSend) || $shouldSend) {
            $this->sendTelegramMessage($chatId, $message);
        } else {
            \Log::info('Telegram reply suppressed due to prior device confirmation', [
                'chat_id' => $chatId
            ]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Send Telegram message with logging
     */
    protected function sendTelegramMessage($chatId, $message)
    {
        $response = TelegramService::send($chatId, $message, 'HTML');

        if ($response === false || 
            (method_exists($response, 'successful') && !$response->successful())) {
            \Log::error('Telegram response failed for webhook message', [
                'chat_id' => $chatId,
                'text' => $message,
                'response' => $response instanceof \Illuminate\Http\Client\Response 
                    ? $response->body() 
                    : $response,
            ]);
        } else {
            \Log::info('Telegram response success for webhook message', [
                'chat_id' => $chatId,
                'message' => $message,
                'response' => $response instanceof \Illuminate\Http\Client\Response 
                    ? $response->body() 
                    : $response,
            ]);
        }
    }
}