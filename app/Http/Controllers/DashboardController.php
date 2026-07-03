<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogMakan;
use App\Services\MqttService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Return daily compliance stats for last N days for the authenticated user.
     */
    public function stats(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $days = (int) $request->query('days', 14);
        $days = max(7, min(60, $days));

        $labels = [];
        $compliance = [];
        $totals = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $label = $date->format('Y-m-d');

            $total = LogMakan::where('user_id', $user->id)
                ->where('tanggal', $label)
                ->count();

            $done = LogMakan::where('user_id', $user->id)
                ->where('tanggal', $label)
                ->where('status', 'sudah')
                ->count();

            $percent = $total > 0 ? round(($done / $total) * 100, 1) : null;

            $labels[] = $label;
            $compliance[] = $percent;
            $totals[] = $total;
        }

        return response()->json([
            'labels' => $labels,
            'compliance' => $compliance,
            'totals' => $totals,
        ]);
    }

    /**
     * Return MQTT device connection status.
     */
    public function mqttStatus(Request $request)
    {
        $mqtt = app(MqttService::class);

        $brokerConnected = $mqtt->testConnection();
        $deviceOnline = $mqtt->isDeviceOnline();
        $connected = $brokerConnected && $deviceOnline;

        return response()->json([
            'connected' => $connected,
            'broker_connected' => $brokerConnected,
            'device_online' => $deviceOnline,
            'status' => $connected ? 'Terhubung' : ($brokerConnected ? 'Broker OK, tapi alat tidak aktif' : 'Terputus'),
            'icon' => $connected ? '🟢' : '🔴',
            'last_seen' => optional($mqtt->getLastDeviceSeen())->toDateTimeString(),
        ]);
    }
}
