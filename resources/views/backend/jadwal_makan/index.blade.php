<x-app-layout>

    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-800">
            Jadwal Makan
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h1 class="text-xl font-semibold text-slate-800">
                    Daftar Jadwal Makan
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola jadwal makan harian Anda.
                </p>

            </div>

            <!-- Button Tambah -->
            <a
                href="{{ route('jadwal-makan.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-700"
            >

                <!-- Icon Plus -->
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"
                    />

                </svg>

                Tambah Jadwal

            </a>

        </div>

        <!-- Success -->
        @if(session('success'))

            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">

                {{ session('success') }}

            </div>

        @endif

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <!-- Head -->
                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                No
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Jam
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Keterangan
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <!-- Body -->
                    <tbody class="divide-y divide-slate-100 bg-white">

                        @forelse($jadwals as $key => $item)

                            <tr class="hover:bg-slate-50 transition">

                                <!-- No -->
                                <td class="px-6 py-4 text-sm text-slate-600">

                                    {{ $jadwals->firstItem() + $key }}

                                </td>

                                <!-- Jam -->
                                <td class="px-6 py-4 text-sm font-medium text-slate-800">

                                    {{ $item->jam }}

                                </td>

                                <!-- Keterangan -->
                                <td class="px-6 py-4 text-sm text-slate-600">

                                    {{ $item->keterangan }}

                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        <!-- Edit -->
                                        <a
                                            href="{{ route('jadwal-makan.edit', $item->id) }}"
                                            class="rounded-lg bg-amber-400 px-4 py-2 text-xs font-medium text-slate-900 transition hover:bg-amber-500"
                                        >

                                            Edit

                                        </a>

                                        <!-- Delete -->
                                        <form
                                            action="{{ route('jadwal-makan.destroy', $item->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin hapus data?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-rose-500 px-4 py-2 text-xs font-medium text-white transition hover:bg-rose-600"
                                            >

                                                Hapus

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >

                                    Belum ada jadwal makan.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->
            <div class="border-t border-slate-200 px-6 py-4">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <p class="text-sm text-slate-500">

                        Menampilkan {{ $jadwals->count() }}
                        dari {{ $jadwals->total() }} data

                    </p>

                    {{ $jadwals->links() }}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>