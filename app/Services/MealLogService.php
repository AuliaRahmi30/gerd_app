<?php

namespace App\Services;

use App\Models\LogMakan;
use App\Models\JadwalMakan;
use Carbon\Carbon;

class MealLogService
{
    /**
     * Create or update a meal log entry
     */
    public function recordMealConfirmation(int $userId, int $jadwalMakanId, string $konfirmasi, ?string $timestamp = null): LogMakan
    {
        $date = Carbon::now()->toDateString();
        $timezone = config('app.timezone', 'UTC');
        $time = $timestamp
            ? Carbon::parse($timestamp)->setTimezone($timezone)->format('H:i:s')
            : Carbon::now($timezone)->format('H:i:s');

        $jadwal = JadwalMakan::find($jadwalMakanId);
        if (!$jadwal) {
            throw new \Exception('Jadwal makan tidak ditemukan');
        }

        $logs = LogMakan::where('user_id', $userId)
            ->where('jadwal_makan_id', $jadwalMakanId)
            ->where('tanggal', $date)
            ->get();

        if ($logs->isEmpty()) {
            return LogMakan::create([
                'user_id' => $userId,
                'jadwal_makan_id' => $jadwalMakanId,
                'tanggal' => $date,
                'jam' => $time,
                'jadwal' => $jadwal->keterangan,
                'status' => 'sudah',
                'konfirmasi' => $konfirmasi,
            ]);
        }

        $updatedLog = null;
        foreach ($logs as $log) {
            if ($log->status === 'sudah') {
                continue;
            }

            if ($log->status === 'telat') {
                $log->update([
                    'jam' => $time,
                    'konfirmasi' => $konfirmasi,
                ]);
                $updatedLog = $log;
                continue;
            }

            $log->update([
                'status' => 'sudah',
                'jam' => $time,
                'konfirmasi' => $konfirmasi,
            ]);
            $updatedLog = $log;
        }

        if ($updatedLog) {
            return $updatedLog;
        }

        return $logs->first(fn (LogMakan $log) => $log->status === 'telat')
            ?? $logs->first(fn (LogMakan $log) => $log->status === 'sudah')
            ?? $logs->first();
    }

    /**
     * Check if meal already confirmed today
     */
    public function isAlreadyConfirmed(int $userId, int $jadwalMakanId): bool
    {
        $date = Carbon::now()->toDateString();

        return LogMakan::where('user_id', $userId)
            ->where('jadwal_makan_id', $jadwalMakanId)
            ->where('tanggal', $date)
            ->where('status', 'sudah')
            ->exists();
    }

    /**
     * Get latest meal confirmation for today
     */
    public function getLatestConfirmationForToday(int $userId): ?LogMakan
    {
        $date = Carbon::now()->toDateString();

        return LogMakan::where('user_id', $userId)
            ->where('tanggal', $date)
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Get the latest schedule for today when there is no existing log yet.
     */
    public function getLatestScheduleForToday(int $userId): ?JadwalMakan
    {
        $currentTime = Carbon::now()->format('H:i:s');

        return JadwalMakan::where('user_id', $userId)
            ->whereRaw('TIME(jam) <= ?', [$currentTime])
            ->orderBy('jam', 'desc')
            ->first();
    }
}
