<?php

namespace App\Console\Commands;

use App\Models\LogMakan;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMealTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:meal-timeout {--force}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Ubah status "menunggu" menjadi "telat" setelah toleransi 10 menit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $toleranceMinutes = 10;
        $now = now(config('app.timezone'));
        $tenMinutesAgo = $now->copy()->subMinutes($toleranceMinutes);

        // Find all logs with 'menunggu' status that passed tolerance
        $expiredLogs = LogMakan::whereIn('status', LogMakan::waitingStatuses())
            ->whereDate('tanggal', today(config('app.timezone')))
            ->whereHas('jadwalMakan', function ($q) use ($tenMinutesAgo) {
                $q->whereTime('jam', '<=', $tenMinutesAgo->format('H:i:s'));
            })
            ->get();

        if ($expiredLogs->isEmpty()) {
            $this->info('✓ No expired meals to update');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($expiredLogs as $log) {
            // Update status to telat
            $log->update(['status' => 'telat']);
            $count++;

            $this->line("  • ID {$log->id}: {$log->jadwalMakan->keterangan} menunggu → telat");

            // Send Telegram notification
            try {
                $this->sendTelegramReminder($log);
            } catch (\Exception $e) {
                $this->warn("    ⚠ Failed to send Telegram reminder: {$e->getMessage()}");
                Log::error('CheckMealTimeout: Failed to send Telegram', [
                    'log_id' => $log->id,
                    'error' => $e->getMessage(),
                ]);
            }

            Log::info('CheckMealTimeout: Status converted to telat', [
                'log_id' => $log->id,
                'jadwal_id' => $log->jadwal_makan_id,
                'created_at' => $log->created_at,
                'current_time' => now(),
            ]);
        }

        $this->info("✓ Successfully converted {$count} meals to 'telat' status");
        
        return Command::SUCCESS;
    }

    /**
     * Send Telegram reminder when auto-converting to telat
     * This is the BACKEND automatic timeout (10 minutes from jadwal creation)
     */
    protected function sendTelegramReminder(LogMakan $log)
    {
        $user = $log->jadwalMakan->user;
        
        if (!$user || !$user->chat_id) {
            Log::warning('Cannot send Telegram: chat_id missing', [
                'user_id' => $log->user_id,
                'log_id' => $log->id,
            ]);
            return;
        }

        try {
            TelegramService::sendLateNotification($user->chat_id, $log->jadwalMakan);

            Log::info('CheckMealTimeout: Telegram reminder sent', [
                'user_id' => $user->id,
                'chat_id' => $user->chat_id,
                'log_id' => $log->id,
            ]);
        } catch (\Exception $e) {
            Log::error('CheckMealTimeout: Telegram send failed', [
                'user_id' => $user->id,
                'chat_id' => $user->chat_id,
                'log_id' => $log->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
