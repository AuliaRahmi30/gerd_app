<div class="min-h-screen bg-slate-50">
    <div class="md:flex md:min-h-screen border-t border-slate-200">

        <!-- Sidebar -->
        <aside class="w-full bg-white md:w-72 md:border-r border-slate-200">

            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">

                <!-- Profile -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 text-slate-900">
                        <x-application-logo class="h-9 w-9 text-slate-900" />
                    </a>

                    <div class="ml-2">
                        <a href="{{ route('profile.edit') }}"
                           class="text-sm font-semibold text-slate-900 hover:underline">
                            {{ optional(Auth::user())->name ?? __('User') }}
                        </a>

                        <div class="text-xs text-slate-500">
                            Profil
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-6 flex flex-col gap-1 text-sm text-slate-700">

                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-100
                       {{ request()->routeIs('dashboard')
                            ? 'bg-slate-100 font-semibold text-slate-900'
                            : 'text-slate-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.707 2.293a1 1 0 00-1.414 0l-8 8A1 1 0 004 11h1v9a1 1 0 001 1h5a1 1 0 001-1v-5h2v5a1 1 0 001 1h5a1 1 0 001-1v-9h1a1 1 0 00.707-1.707l-8-8z" />
                        </svg>
                        Dashboard Monitoring
                    </a>

                    <a href="{{ route('jadwal-makan.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-100
                       {{ request()->routeIs('jadwal-makan.*')
                            ? 'bg-slate-100 font-semibold text-slate-900'
                            : 'text-slate-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5 5a1 1 0 011-1h1V2.5a.5.5 0 011 0V4h8V2.5a.5.5 0 011 0V4h1a1 1 0 011 1v3H5V5zm14 5H5v9a2 2 0 002 2h10a2 2 0 002-2v-9zm-9 3h2v2H10v-2zm4 0h2v2h-2v-2zm-4 4h2v2H10v-2zm4 0h2v2h-2v-2z" />
                        </svg>
                        Jadwal Makan
                    </a>

                    <a href="{{ route('log-makan.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-100
                       {{ request()->routeIs('log-makan.*')
                            ? 'bg-slate-100 font-semibold text-slate-900'
                            : 'text-slate-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 7a1 1 0 011-1h4a1 1 0 011 1v1H9V7z" />
                            <path fill-rule="evenodd" d="M5 4a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2H5zm14 2H5v12h14V6zm-8 9h6a1 1 0 010 2H11a1 1 0 010-2zm0-4h6a1 1 0 010 2H11a1 1 0 010-2z" clip-rule="evenodd" />
                        </svg>
                        Riwayat Makan
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="mt-4 flex items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-100
                       {{ request()->routeIs('profile.edit')
                            ? 'bg-slate-100 font-semibold text-slate-900'
                            : 'text-slate-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 6a4 4 0 100 8 4 4 0 000-8zm-5.5 11.5a5.5 5.5 0 0111 0 .75.75 0 01-.75.75h-9a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
                        </svg>
                        Profil
                    </a>

                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div class="flex-1 bg-slate-50">

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

                @isset($header)
                    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        {{ $header }}
                    </div>
                @endisset

                {{ $slot }}

            </main>
        </div>
    </div>
</div>