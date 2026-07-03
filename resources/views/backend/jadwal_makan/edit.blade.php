<x-app-layout>

    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-800">
            Edit Jadwal Makan
        </h2>
    </x-slot>

    <div class="max-w-2xl">

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
                action="{{ route('jadwal-makan.update', $jadwalMakan->id) }}"
                method="POST"
                class="space-y-6"
            >

                @csrf
                @method('PUT')

                <!-- Jam -->
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Jam Makan
                    </label>

                    <input
                        type="time"
                        name="jam"
                        value="{{ old('jam', $jadwalMakan->jam) }}"
                        step="1"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >

                </div>

                <!-- Keterangan -->
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Keterangan
                    </label>

                    <input
                        type="text"
                        name="keterangan"
                        value="{{ old('keterangan', $jadwalMakan->keterangan) }}"
                        placeholder="Contoh: Sarapan"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >

                </div>

                <!-- Button -->
                <div class="flex items-center justify-end gap-3 pt-4">

                    <a
                        href="{{ route('jadwal-makan.index') }}"
                        class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 transition"
                    >

                        Batal

                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition"
                    >

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>