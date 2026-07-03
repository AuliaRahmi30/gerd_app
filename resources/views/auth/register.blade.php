<x-guest-layout>

    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="rounded-3xl bg-white p-8 shadow-lg ring-1 ring-slate-200">

            <!-- Header -->
            <div class="mb-8 text-center">

                <h2 class="text-2xl font-bold text-slate-800">
                    Daftar
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Buat akun GERDCare
                </p>

            </div>

            <!-- Session Status -->
            <x-auth-session-status
                class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                :status="session('status')"
            />

            <!-- Form -->
            <form
                method="POST"
                action="{{ route('register') }}"
                class="space-y-5"
            >

                @csrf

                <!-- Nama -->
                <div>

                    <x-input-label
                        for="name"
                        :value="__('Nama')"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    />

                    <x-text-input
                        id="name"
                        type="text"
                        name="name"
                        :value="old('name')"
                        required
                        autofocus
                        autocomplete="name"
                        class="block w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-200"
                    />

                    <x-input-error
                        :messages="$errors->get('name')"
                        class="mt-2 text-sm text-rose-600"
                    />

                </div>

                <!-- Email -->
                <div>

                    <x-input-label
                        for="email"
                        :value="__('Email')"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    />

                    <x-text-input
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autocomplete="username"
                        class="block w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-200"
                    />

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-2 text-sm text-rose-600"
                    />

                </div>

                <!-- Password -->
                <div>

                    <x-input-label
                        for="password"
                        :value="__('Password')"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    />

                    <x-text-input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="block w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-200"
                    />

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2 text-sm text-rose-600"
                    />

                </div>

                <!-- Konfirmasi Password -->
                <div>

                    <x-input-label
                        for="password_confirmation"
                        :value="__('Konfirmasi Password')"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    />

                    <x-text-input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="block w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-200"
                    />

                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >

                    Daftar

                </button>

            </form>

            <!-- Login -->
            <p class="mt-6 text-center text-sm text-slate-500">

                Sudah punya akun?

                <a
                    href="{{ route('login') }}"
                    class="font-semibold text-emerald-600 hover:text-emerald-700"
                >

                    Login

                </a>

            </p>

        </div>

    </div>

</x-guest-layout>