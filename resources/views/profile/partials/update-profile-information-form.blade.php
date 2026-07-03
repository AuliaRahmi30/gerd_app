<section>
    <header>
        <h2 class="text-lg font-medium text-slate-900">Informasi Profil</h2>
        <p class="mt-1 text-sm text-slate-500">Perbarui nama, email, dan ID Telegram Anda di sini.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                :value="old('name', $user->name)"
                required
                autofocus
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                :value="old('email', $user->email)"
                required
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="chat_id" :value="__('Telegram Chat ID')" />
            <x-text-input
                id="chat_id"
                name="chat_id"
                type="text"
                class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                :value="old('chat_id', $user->chat_id)"
            />
            <p class="mt-2 text-sm text-slate-500">Masukkan ID Telegram yang Anda dapatkan setelah mengirim /start ke bot.</p>
            <x-input-error class="mt-2" :messages="$errors->get('chat_id')" />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <x-primary-button class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold hover:bg-emerald-700 focus:ring-emerald-500">
                {{ __('Simpan Perubahan') }}
            </x-primary-button>

            @if (session('success'))
                <p class="text-sm text-emerald-700">{{ session('success') }}</p>
            @endif
        </div>
    </form>
</section>