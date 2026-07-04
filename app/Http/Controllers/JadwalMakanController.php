<?php

namespace App\Http\Controllers;

use App\Models\JadwalMakan;
use App\Models\LogMakan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\TelegramService;

class JadwalMakanController extends Controller
{
    /**
     * Tampilkan data milik user yang login
     */
    public function index()
    {
        $jadwals = JadwalMakan::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('backend.jadwal_makan.index', compact('jadwals'));
    }

    /**
     * Form tambah data
     */
    public function create()
    {
        return view('backend.jadwal_makan.create');
    }

    /**
     * Simpan data
     */
    public function store(Request $request)
    {
        $request->validate([
            'jam' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'keterangan' => 'required|string|max:255',
        ]);

        // Normalisasi jam ke format H:i:s
        $jam = Carbon::parse($request->jam)->format('H:i:s');

        // ============================================================
        // 1. SIMPAN JADWAL
        // ============================================================
        $jadwal = JadwalMakan::create([
            'user_id' => auth()->id(),
            'jam' => $jam,
            'keterangan' => $request->keterangan,
        ]);

        // ============================================================
        // 2. BUAT LOG MAKAN OTOMATIS (TANPA OBSERVER)
        // ============================================================
        $today = Carbon::now()->toDateString();
        
        LogMakan::create([
            'user_id' => auth()->id(),
            'jadwal_makan_id' => $jadwal->id,
            'tanggal' => $today,
            'jam' => $jadwal->jam,
            'jadwal' => $jadwal->keterangan,
            'status' => LogMakan::STATUS_WAITING,
            'konfirmasi' => null,
        ]);

        // ============================================================
        // 3. KIRIM NOTIFIKASI TELEGRAM
        // ============================================================
        $user = auth()->user();
        if ($user && !empty($user->chat_id)) {
            TelegramService::sendScheduleCreated($user->chat_id, $jadwal->jam, $jadwal->keterangan);
        }

        return redirect()->route('jadwal-makan.index')
            ->with('success', 'Jadwal makan berhasil ditambahkan');
    }

    /**
     * Detail data
     */
    public function show(JadwalMakan $jadwalMakan)
    {
        if ($jadwalMakan->user_id != auth()->id()) {
            abort(403);
        }

        return view('backend.jadwal_makan.show', compact('jadwalMakan'));
    }

    /**
     * Form edit
     */
    public function edit(JadwalMakan $jadwalMakan)
    {
        if ($jadwalMakan->user_id != auth()->id()) {
            abort(403);
        }

        return view('backend.jadwal_makan.edit', compact('jadwalMakan'));
    }

    /**
     * Update data
     */
    public function update(Request $request, JadwalMakan $jadwalMakan)
    {
        if ($jadwalMakan->user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'jam' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'keterangan' => 'required|string|max:255',
        ]);

        // Normalisasi jam ke format H:i:s
        $jam = Carbon::parse($request->jam)->format('H:i:s');

        // ============================================================
        // 1. UPDATE JADWAL
        // ============================================================
        $jadwalMakan->update([
            'jam' => $jam,
            'keterangan' => $request->keterangan,
        ]);

        // ============================================================
        // 2. PERBARUI LOG MAKAN HARI INI ATAU BUAT BARU JIKA BELUM ADA
        // ============================================================
        $today = Carbon::now()->toDateString();

        $todayLog = LogMakan::where('jadwal_makan_id', $jadwalMakan->id)
            ->where('tanggal', $today)
            ->latest('id')
            ->first();

        if ($todayLog) {
            $todayLog->update([
                'jam' => $jadwalMakan->jam,
                'jadwal' => $jadwalMakan->keterangan,
            ]);
        } else {
            LogMakan::create([
                'user_id' => auth()->id(),
                'jadwal_makan_id' => $jadwalMakan->id,
                'tanggal' => $today,
                'jam' => $jadwalMakan->jam,
                'jadwal' => $jadwalMakan->keterangan,
                'status' => LogMakan::STATUS_WAITING,
                'konfirmasi' => null,
            ]);
        }

        // ============================================================
        // 3. KIRIM NOTIFIKASI TELEGRAM
        // ============================================================
        $user = auth()->user();
        if ($user && !empty($user->chat_id)) {
            TelegramService::sendScheduleUpdated($user->chat_id, $jadwalMakan->jam, $jadwalMakan->keterangan);
        }

        return redirect()->route('jadwal-makan.index')
            ->with('success', 'Jadwal makan berhasil diupdate');
    }

    /**
     * Hapus data
     */
    public function destroy(JadwalMakan $jadwalMakan)
    {
        if ($jadwalMakan->user_id != auth()->id()) {
            abort(403);
        }

        // ============================================================
        // 1. HAPUS LOG MAKAN (yang masih menunggu)
        // ============================================================
        LogMakan::where('jadwal_makan_id', $jadwalMakan->id)
            ->whereIn('status', LogMakan::waitingStatuses())
            ->delete();

        // ============================================================
        // 2. HAPUS JADWAL
        // ============================================================
        $jadwalMakan->delete();

        // ============================================================
        // 3. KIRIM NOTIFIKASI TELEGRAM
        // ============================================================
        $user = auth()->user();
        if ($user && !empty($user->chat_id)) {
            TelegramService::sendScheduleDeleted($user->chat_id, $jadwalMakan->jam, $jadwalMakan->keterangan);
        }

        return redirect()->route('jadwal-makan.index')
            ->with('success', 'Jadwal makan berhasil dihapus');
    }
}