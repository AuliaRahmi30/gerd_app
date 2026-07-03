<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalMakan;
use App\Services\MealLogService;
use App\Services\TelegramService;
use Carbon\Carbon;

class DeviceController extends Controller
{
    /**
     * Accept confirmation from device (push button).
     * Expected JSON: { "jadwal_makan_id": 12, "timestamp": "2026-05-31T07:00:00Z" }
     * Authenticate via header X-Device-Token matching config('services.device.confirm_token')
     */
    public function confirm(Request $request)
    {
        $token = $request->header('X-Device-Token') ?: $request->query('token') ?: $request->input('device_token');
        $configuredToken = trim((string) config('services.device.confirm_token'));

        if ($configuredToken !== '' && (! $token || $token !== $configuredToken)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $jadwalId = $request->input('jadwal_makan_id', $request->input('jadwal_id', $request->input('id')));
        $userId = $request->input('user_id', $request->input('userId'));
        $rawTimestamp = $request->input('timestamp', $request->input('time', $request->input('jam', $request->input('confirmed_at'))));

        $request->merge([
            'jadwal_makan_id' => $jadwalId,
            'user_id' => $userId,
        ]);

        $request->validate([
            'jadwal_makan_id' => 'required|integer|exists:jadwal_makans,id',
            'user_id' => 'sometimes|nullable|integer|exists:users,id',
        ]);

        $jadwalId = $request->jadwal_makan_id;
        $userId = $request->user_id ?: null;

        $jadwal = JadwalMakan::find($jadwalId);
        if (! $jadwal) {
            return response()->json(['error' => 'jadwal not found'], 404);
        }

        // prefer provided user_id, otherwise use jadwal owner
        $userId = $userId ?: $jadwal->user_id;

        $date = Carbon::now()->toDateString();
        $time = null;

        if ($rawTimestamp) {
            try {
                $time = Carbon::parse($rawTimestamp)->format('H:i:s');
            } catch (\Throwable $e) {
                \Log::warning('DeviceController: invalid timestamp received', [
                    'timestamp' => $rawTimestamp,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $time = $time ?: Carbon::now()->format('H:i:s');

        $mealLogService = app(MealLogService::class);
        $mealLogService->recordMealConfirmation($userId, $jadwalId, 'device', $time);

        if ($jadwal->user && $jadwal->user->chat_id) {
            try {
                TelegramService::sendConfirmationReceived($jadwal->user->chat_id, $jadwal->keterangan);
            } catch (\Throwable $e) {
                \Log::error('DeviceController: Telegram send failed', [
                    'chat_id' => $jadwal->user->chat_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }
}
