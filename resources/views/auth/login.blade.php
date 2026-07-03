<x-guest-layout>

    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="rounded-3xl bg-white p-8 shadow-lg ring-1 ring-slate-200">

            <!-- Header -->
            <div class="mb-8 text-center">

                <h2 class="text-2xl font-bold text-slate-800">
                    Login
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Masuk ke akun GERDCare
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
                action="{{ route('login') }}"
                class="space-y-5"
            >

                @csrf

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
                        autofocus
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
                        autocomplete="current-password"
                        class="block w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-200"
                    />

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2 text-sm text-rose-600"
                    />

                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between text-sm">

                    <label
                        for="remember_me"
                        class="flex items-center gap-2 text-slate-600"
                    >

                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                        >

                        <span>Remember me</span>

                    </label>

                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >

                    Login

                </button>

            </form>

            <!-- Register -->
            <p class="mt-6 text-center text-sm text-slate-500">

                Belum punya akun?

                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-emerald-600 hover:text-emerald-700"
                >

                    Daftar

                </a>

            </p>

        </div>

    </div>

</x-guest-layout>