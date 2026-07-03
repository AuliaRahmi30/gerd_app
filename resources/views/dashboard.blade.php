<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold text-slate-900">Dashboard GERDCare</h2>
            <span class="text-sm text-slate-500">Ringkas jadwal makan Anda hari ini.</span>
        </div>
    </x-slot>

    @php
        $jadwals = \App\Models\JadwalMakan::where('user_id', auth()->id())->orderBy('jam')->get();
        $totalJadwal = $jadwals->count();
        $todayLogs = \App\Models\LogMakan::where('user_id', auth()->id())
            ->whereDate('tanggal', now())
            ->get();
        $todayCompletedCount = $todayLogs->where('status', 'sudah')->count();
        $todayLateCount = $todayLogs->where('status', 'telat')->count();
        $todayPendingCount = max(0, $totalJadwal - $todayLogs->count());
        $todayCompletionRate = $totalJadwal > 0 ? round($todayCompletedCount / $totalJadwal * 100) : 0;
    @endphp

    <div class="space-y-8">

        <!-- HERO SECTION -->
        <div class="overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-5 text-white shadow-lg">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold">Halo, {{ auth()->user()->name }} 👋</h1>
                    <p class="mt-1 text-sm text-emerald-100">Pantau jadwal makan dan kepatuhan GERD Anda setiap hari.</p>
                </div>
                <div>
                    <div class="rounded-xl bg-white/20 px-4 py-3 backdrop-blur">
                        <p class="text-xs font-medium">Kepatuhan Hari Ini</p>
                        <h2 class="mt-1 text-3xl font-bold">{{ $todayCompletionRate }}%</h2>
                        <p class="mt-1 text-xs text-emerald-100">
                            @if($todayCompletionRate >= 75)
                                Sangat Baik
                            @elseif($todayCompletionRate >= 50)
                                Cukup Baik
                            @else
                                Perlu Ditingkatkan
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 2.75a.75.75 0 01.75.75v1h9.5V3.5a.75.75 0 011.5 0v1.25h.75A2.75 2.75 0 0121 7.25v11.5A2.75 2.75 0 0118.25 21H5.75A2.75 2.75 0 013 18.25V7.25A2.75 2.75 0 015.75 4.5H6V3.5A.75.75 0 016 2.75zM5.75 6.5a.25.25 0 00-.25.25v1.75h13.5V6.75a.25.25 0 00-.25-.25H5.75zm0 3.75v10.5h12.5V10.25H5.75z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jumlah Jadwal</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $totalJadwal }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2.75a.75.75 0 01.75.75V7H13.5V3.5a.75.75 0 011.5 0V7h.75a2.75 2.75 0 012.75 2.75v8a2.75 2.75 0 01-2.75 2.75H6.25A2.75 2.75 0 013.5 17.75v-8A2.75 2.75 0 016.25 7H7V3.5A.75.75 0 017.75 2.75zM6.25 9.5a.25.25 0 00-.25.25v6.75c0 .138.112.25.25.25h11.5a.25.25 0 00.25-.25V9.75a.25.25 0 00-.25-.25H6.25z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sudah Makan</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $todayCompletedCount }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 110-16 8 8 0 010 16zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Telat Makan</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $todayLateCount }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kepatuhan</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $todayCompletionRate }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART AND SCHEDULE SECTION -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- COMPLIANCE CHART -->
            <div class="lg:col-span-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Kepatuhan Makan 14 Hari Terakhir</h3>
                        </div>
                        <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                            <span class="text-xs text-slate-500">Periode</span>
                            <select id="daysSelect" class="bg-transparent text-sm font-medium outline-none cursor-pointer">
                                <option value="7">7 Hari Terakhir</option>
                                <option value="14" selected>14 Hari</option>
                                <option value="30">30 Hari</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6">
                        <canvas id="complianceChart" class="w-full" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- TODAY'S SCHEDULE -->
            <div class="lg:col-span-1">
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100 h-full">
                    <div class="flex flex-col gap-2">
                        <h4 class="text-sm font-semibold text-slate-800">Jadwal Makan Hari Ini</h4>
                        <p class="text-xs text-slate-500">Lihat jadwal yang sudah diikuti dan yang masih perlu Anda ingat.</p>
                    </div>

                    <ul class="mt-6 space-y-2 max-h-96 overflow-y-auto">
                        @forelse(\App\Models\JadwalMakan::where('user_id', auth()->id())->orderBy('jam')->get() as $j)
                            @php
                                $title = strtolower($j->keterangan ?? '');
                                $log = \App\Models\LogMakan::whereDate('tanggal', now())->where('jadwal_makan_id', $j->id)->latest('id')->first();
                                $status = 'telat';
                                if ($log) {
                                    $status = $log->status;
                                } else {
                                    $currentTime = now()->format('H:i:s');
                                    if ($currentTime < $j->jam) {
                                        $status = 'menunggu';
                                    }
                                }
                                if (str_contains($title, 'sarapan')) {
                                    $iconBg = 'bg-emerald-100 text-emerald-700';
                                    $icon = '☀️';
                                } elseif (str_contains($title, 'snack') || str_contains($title, 'pagi')) {
                                    $iconBg = 'bg-sky-100 text-sky-700';
                                    $icon = '🍪';
                                } elseif (str_contains($title, 'siang')) {
                                    $iconBg = 'bg-amber-100 text-amber-700';
                                    $icon = '🕐';
                                } elseif (str_contains($title, 'sore')) {
                                    $iconBg = 'bg-orange-100 text-orange-700';
                                    $icon = '🧡';
                                } else {
                                    $iconBg = 'bg-purple-100 text-purple-700';
                                    $icon = '🌙';
                                }
                                $jamFormatted = \Carbon\Carbon::createFromFormat('H:i:s', $j->jam)->format('H:i');
                            @endphp
                            <li class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="text-lg">{{ $icon }}</div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $j->keterangan ?? 'Makan' }}</p>
                                        <p class="text-xs text-slate-500">{{ $jamFormatted }} WIB</p>
                                    </div>
                                </div>
                                <div>
                                    @if($status == 'sudah')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Sudah
                                        </span>
                                    @elseif($status == 'menunggu')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-700 whitespace-nowrap">
                                            Menunggu
                                        </span>
                                    @elseif($status == 'telat')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 whitespace-nowrap">
                                            Telat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 whitespace-nowrap">
                                            Telat
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="py-8 text-center">
                                <p class="text-sm text-slate-500">Belum ada jadwal makan</p>
                                <a href="{{ route('jadwal-makan.index') }}" class="mt-2 inline-block text-xs font-semibold text-emerald-600 hover:text-emerald-700">Buat Jadwal</a>
                            </li>
                        @endforelse
                    </ul>

                    <a href="{{ route('jadwal-makan.index') }}" class="mt-4 block w-full rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-center text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                        Lihat semua jadwal
                    </a>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('complianceChart').getContext('2d');
            let complianceChart;

            function formatLabelISO(iso) {
                try {
                    const d = new Date(iso + 'T00:00:00');
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                } catch (e) { return iso; }
            }

            async function loadStats(days = 14) {
                const res = await fetch(`/dashboard/logs-stats?days=${days}`);
                if (!res.ok) return;
                const data = await res.json();

                const rawLabels = data.labels;
                const labels = rawLabels.map(l => formatLabelISO(l));
                const compliance = data.compliance.map(v => v === null ? null : v);
                const totals = data.totals;

                const todayIso = new Date().toISOString().slice(0, 10);
                const todayIndex = rawLabels.indexOf(todayIso);

                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, 'rgba(16,185,129,0.3)');
                gradient.addColorStop(1, 'rgba(16,185,129,0.05)');

                const cfg = {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Kepatuhan (%)',
                            data: compliance,
                            borderColor: '#059669',
                            backgroundColor: gradient,
                            spanGaps: true,
                            tension: 0.4,
                            yAxisID: 'y',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            borderWidth: 2.5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            x: { 
                                display: true,
                                grid: { display: false, drawBorder: false }
                            },
                            y: {
                                type: 'linear',
                                position: 'left',
                                min: 0,
                                max: 100,
                                ticks: { 
                                    callback: v => v + '%',
                                    stepSize: 25
                                },
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                title: { display: false }
                            }
                        },
                        plugins: {
                            legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 15 } },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                padding: 12,
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                cornerRadius: 8,
                                callbacks: {
                                    title: (ctx) => ctx[0] ? ctx[0].label : '',
                                    label: (ctx) => {
                                        const idx = ctx.dataIndex;
                                        const val = ctx.parsed.y;
                                        if (totals[idx] === 0) return 'Tidak ada data';
                                        return `Kepatuhan: ${val === null ? '—' : val + '%'} (${totals[idx]} log)`;
                                    }
                                }
                            }
                        }
                    },
                    plugins: [{
                        id: 'todayLine',
                        afterDatasetsDraw: (chart) => {
                            const idx = chart.$todayIndex;
                            if (typeof idx !== 'number' || idx < 0) return;
                            const xScale = chart.scales.x;
                            const x = xScale.getPixelForValue(idx);
                            const ctx2 = chart.ctx;
                            ctx2.save();
                            ctx2.beginPath();
                            ctx2.moveTo(x, chart.chartArea.top);
                            ctx2.lineTo(x, chart.chartArea.bottom);
                            ctx2.strokeStyle = 'rgba(99,102,241,0.15)';
                            ctx2.lineWidth = 2;
                            ctx2.stroke();
                            ctx2.restore();
                        }
                    }]
                };

                if (complianceChart) {
                    complianceChart.data = cfg.data;
                    complianceChart.options = cfg.options;
                    complianceChart.plugins = cfg.plugins;
                    complianceChart.$todayIndex = todayIndex;
                    complianceChart.update();
                } else {
                    complianceChart = new Chart(ctx, cfg);
                    complianceChart.$todayIndex = todayIndex;
                }
            }

            document.getElementById('daysSelect').addEventListener('change', function() {
                loadStats(this.value);
            });

            loadStats(14);
        </script>

    </div>
</x-app-layout>