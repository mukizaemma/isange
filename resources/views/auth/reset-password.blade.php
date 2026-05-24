<x-guest-layout>
    <x-authentication-card :subtitle="__('Choose a new password')">
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-neutral-700" />
                <x-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" class="text-neutral-700" />
                <x-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-neutral-700" />
                <x-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="space-y-4 pt-2">
                <button type="submit" class="auth-btn-primary w-full py-3 normal-case tracking-normal">
                    {{ __('Reset Password') }}
                </button>

                <p class="text-center text-sm">
                    <a class="font-medium text-[#b87620] transition hover:text-[#8f5a18] focus:outline-none focus-visible:underline" href="{{ route('login') }}">
                        ← {{ __('Back to sign in') }}
                    </a>
                </p>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
