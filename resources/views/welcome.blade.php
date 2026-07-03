<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GERD Monitoring</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-900 antialiased">

    <div class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(16,185,129,0.10),_transparent_40%),radial-gradient(circle_at_bottom_left,_rgba(16,185,129,0.12),_transparent_30%)]">

        <!-- Navbar -->
        <header class="border-b border-slate-200 bg-white/95 backdrop-blur-xl shadow-sm">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg">
                        <span class="text-lg font-semibold">G</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-slate-900">GERDCare</h1>
                        <p class="text-xs uppercase tracking-[0.24em] text-emerald-500">Monitoring GERD</p>
                    </div>
                </div>

            </div>
        </header>

        <!-- Main -->
        <main class="mx-auto max-w-6xl px-6 py-12 md:py-16">
            <section class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div class="space-y-8">
                    <div>
                        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">Pantau jadwal makan Anda dengan mudah dan jaga kesehatan lambung setiap hari.</h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">GERDCare adalah platform monitoring yang membantu menata jadwal makan, mencatat riwayat, dan memberikan pengingat sehingga Anda dapat mengikuti pola makan sehat secara konsisten.</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700">Mulai Sekarang</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-emerald-600 bg-white px-5 py-3 text-sm font-semibold text-emerald-700 shadow-sm hover:bg-emerald-50">Masuk</a>
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700">Daftar</a>
                        @endauth
                        <a href="#fitur" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-700">Lihat Fitur</a>
                    </div>

                </div>

                <div class="relative overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 via-white to-slate-50"></div>
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1200&q=80" alt="Makanan sehat" class="relative h-[400px] w-full object-cover" />
                </div>
            </section>

            <section id="fitur" class="mt-16 grid gap-6 md:grid-cols-3">
                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">Jadwal Makan</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Atur slot waktu makan dan dapatkan rekomendasi jadwal yang terstruktur.</p>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 8h10M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">Log Makan</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Pantau kepatuhan dan perkembangan pola makan setiap hari.</p>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">Pengingat</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Notifikasi tepat waktu agar jadwal makan tidak terlewatkan.</p>
                </div>
            </section>
        </main>

    </div>

</body>
</html>