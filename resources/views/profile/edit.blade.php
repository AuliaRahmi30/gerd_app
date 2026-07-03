<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold text-slate-900">Profil Saya</h2>
            <span class="text-sm text-slate-500">Ubah informasi akun dan pengaturan notifikasi Telegram Anda.</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[280px_1fr]">
            <!-- Sidebar -->
            <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm h-fit sticky top-[108px]">
                <div class="flex flex-col items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-2xl font-semibold text-emerald-700">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="text-center">
                        <p class="text-base font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">ID: {{ auth()->user()->id }}</p>
                    </div>
                </div>

                <div class="mt-6 space-y-3 border-t border-slate-200 pt-6 text-sm text-slate-600">
                    <p>Perbarui data akun Anda di halaman ini.</p>
                    <p>Email dan Telegram Chat ID harus benar untuk menerima notifikasi.</p>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="space-y-6">
                <!-- Update Profile Form -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Delete Account Form -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

</x-app-layout>