<x-app-layout>

    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-800">
            Edit Log Makan
        </h2>
    </x-slot>

    <div class="max-w-3xl">

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">

            <!-- Error -->
            @if ($errors->any())

                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">

                    <ul class="list-disc space-y-1 pl-5">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <!-- Form -->
            <form
                method="POST"
                action="{{ route('log-makan.update', $logMakan->id) }}"
                class="space-y-6"
            >

                @csrf
                @method('PUT')

                <!-- Tanggal & Jam -->
                <div class="grid gap-6 sm:grid-cols-2">

                    <!-- Tanggal -->
                    <div>

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            value="{{ old('tanggal', $logMakan->tanggal) }}"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >

                    </div>

                    <!-- Jam -->
                    <div>

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Jam
                        </label>

                        <input
                            type="time"
                            name="jam"
                            value="{{ old('jam', $logMakan->jam) }}"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >

                    </div>

                </div>

                <!-- Jadwal -->
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Jadwal
                    </label>

                    <input
                        type="text"
                        name="jadwal"
                        value="{{ old('jadwal', $logMakan->jadwal) }}"
                        placeholder="Contoh: Sarapan"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >

                </div>

                <!-- Status & Konfirmasi -->
                <div class="grid gap-6 sm:grid-cols-2">

                    <!-- Status -->
                    <div>

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Status
                        </label>

                        <select
                            name="status"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >

                            <option value="">
                                -- Pilih Status --
                            </option>

                            <option
                                value="sudah"
                                {{ old('status', $logMakan->status) == 'sudah' ? 'selected' : '' }}
                            >
                                Sudah Makan
                            </option>


                            <option
                                value="menunggu"
                                {{ old('status', $logMakan->status) == 'menunggu' ? 'selected' : '' }}
                            >
                                Menunggu
                            </option>

                            <option
                                value="telat"
                                {{ old('status', $logMakan->status) == 'telat' ? 'selected' : '' }}
                            >
                                Telat Makan
                            </option>

                        </select>

                    </div>

                    <!-- Konfirmasi -->
                    <div>

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Konfirmasi
                        </label>

                        <input
                            type="text"
                            name="konfirmasi"
                            value="{{ old('konfirmasi', $logMakan->konfirmasi) }}"
                            placeholder="Contoh: Telegram"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >

                    </div>

                </div>

                <!-- Button -->
                <div class="flex items-center justify-end gap-3 pt-4">

                    <a
                        href="{{ route('log-makan.index') }}"
                        class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 transition"
                    >

                        Batal

                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition"
                    >

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>