<x-guest-layout>
    <x-authentication-card :subtitle="__('Martin Aviator Hotel · Admin')">
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-neutral-700" />
                <x-input
                    id="email"
                    class="mt-2 block w-full"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" class="text-neutral-700" />
                <x-input
                    id="password"
                    class="mt-2 block w-full"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />
            </div>

            <div class="flex items-center">
                <label for="remember_me" class="flex cursor-pointer items-center">
                    <x-checkbox id="remember_me" name="remember" class="rounded border-neutral-300 text-[#e69138] focus:ring-[#e69138]" />
                    <span class="ml-2 text-sm text-neutral-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="auth-btn-primary w-full py-3 normal-case tracking-normal">
                    {{ __('Log in') }}
                </button>
            </div>

            <div class="flex flex-col gap-3 border-t border-neutral-100 pt-6 text-center text-sm sm:flex-row sm:justify-between sm:text-left">
                @if (Route::has('password.request'))
                    <a
                        class="font-medium text-[#b87620] transition hover:text-[#8f5a18] focus:outline-none focus-visible:underline"
                        href="{{ route('password.request') }}"
                    >
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                @if (Route::has('register'))
                    <a
                        class="font-medium text-neutral-600 transition hover:text-neutral-900 focus:outline-none focus-visible:underline sm:text-right"
                        href="{{ route('register') }}"
                    >
                        {{ __('Create an account') }}
                    </a>
                @endif
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
