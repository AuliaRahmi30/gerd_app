<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>GERDCare Monitoring</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    <!-- Overlay Mobile -->
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 z-20 bg-slate-900/30 md:hidden"
        @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-72 transform border-r border-slate-100 bg-white shadow-xl transition duration-300 ease-in-out md:translate-x-0"
    >
        <div class="flex h-[92px] items-center gap-4 border-b border-slate-200 bg-gradient-to-r from-emerald-50 to-white px-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white shadow-lg">
                <span class="text-lg font-bold">G</span>
            </div>
            <div>
                <h1 class="text-xl font-semibold text-slate-900">GERDCare</h1>
                <p class="text-xs uppercase tracking-[0.24em] text-emerald-500">Monitoring</p>
            </div>
        </div>

        <nav class="space-y-2 p-4">
            @php $active = 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md font-semibold'; @endphp

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition {{ request()->routeIs('dashboard') ? $active : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l7-7 7 7M5 12v7a2 2 0 002 2h2V12h6v9h2a2 2 0 002-2v-7" />
                </svg>
                Dashboard Monitoring
            </a>

            <a href="{{ route('jadwal-makan.index') }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition {{ request()->routeIs('jadwal-makan.*') ? $active : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 3h8a2 2 0 012 2v2H6V5a2 2 0 012-2zM6 9h12v12H6V9z" />
                </svg>
                Jadwal Makan
            </a>

            <a href="{{ route('log-makan.index') }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition {{ request()->routeIs('log-makan.*') ? $active : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14M5 8h14M5 12h14M5 16h14M5 20h14" />
                </svg>
                Log Makan
            </a>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition {{ request()->routeIs('profile.edit') ? $active : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 20a6 6 0 0112 0" />
                </svg>
                Profil
            </a>
        </nav>

        <div class="mt-auto border-t border-slate-200 p-4">
            <form method="POST" action="{{ route('logout') }}" class="mt-48">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 8v8" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 md:ml-72">
        <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/90 backdrop-blur-xl">
            <div class="flex h-[92px] items-center justify-between px-4 md:px-8">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="rounded-2xl border border-slate-200 bg-white p-2 text-slate-700 shadow-sm transition hover:bg-slate-50 md:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-full border border-slate-200 bg-white px-3 py-2 shadow-sm transition hover:shadow-md">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600 text-sm font-semibold text-white">
                        {{ strtoupper(substr(optional(Auth::user())->name ?? 'U', 0, 1)) }}
                    </span>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-slate-900">{{ optional(Auth::user())->name ?? 'User' }}</p>
                        <p class="text-xs text-slate-500">Profil</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </header>

        <main class="p-4 md:p-8">
            {{ $slot }}
        </main>
    </div>
</div>

</body>
</html>