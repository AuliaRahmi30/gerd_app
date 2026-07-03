<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-slate-900">Hapus Akun</h2>

        <p class="mt-1 text-sm text-slate-500">
            Akun yang dihapus tidak bisa dikembalikan. Pastikan Anda sudah menyimpan data yang penting terlebih dahulu.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-full px-5 py-3 text-sm font-semibold"
    >Hapus Akun</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-slate-900">Yakin ingin menghapus akun?</h2>

            <p class="mt-2 text-sm text-slate-500">
                Semua data akun akan hilang secara permanen. Masukkan password Anda untuk mengonfirmasi.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Password"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button class="rounded-full px-5 py-3">
                    Hapus Akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
