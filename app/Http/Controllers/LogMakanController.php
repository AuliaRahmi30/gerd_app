<?php

namespace App\Http\Controllers;

use App\Models\LogMakan;
use Illuminate\Http\Request;

class LogMakanController extends Controller
{
    public function index()
    {
        $logs = LogMakan::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('backend.log_makan.index', compact('logs'));
    }

    public function create()
    {
        return view('backend.log_makan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'jadwal' => 'required',
            'status' => 'required',
            'konfirmasi' => 'nullable',
        ]);

        LogMakan::create([
            'user_id' => auth()->id(),
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'jadwal' => $request->jadwal,
            'status' => $request->status,
            'konfirmasi' => $request->konfirmasi,
        ]);

        return redirect()->route('log-makan.index')
            ->with('success', 'Log makan berhasil ditambahkan');
    }

    public function edit(LogMakan $logMakan)
    {
        if ($logMakan->user_id != auth()->id()) {
            abort(403);
        }

        return view('backend.log_makan.edit', compact('logMakan'));
    }

    public function update(Request $request, LogMakan $logMakan)
    {
        if ($logMakan->user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'jadwal' => 'required',
            'status' => 'required',
            'konfirmasi' => 'nullable',
        ]);

        $logMakan->update([
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'jadwal' => $request->jadwal,
            'status' => $request->status,
            'konfirmasi' => $request->konfirmasi,
        ]);

        return redirect()->route('log-makan.index')
            ->with('success', 'Log makan berhasil diupdate');
    }

    public function destroy(LogMakan $logMakan)
    {
        if ($logMakan->user_id != auth()->id()) {
            abort(403);
        }

        $logMakan->delete();

        return back()->with('success', 'Log makan berhasil dihapus');
    }
}