<x-guest-layout>
    <x-authentication-card :subtitle="__('Reset password')">
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-6 rounded-lg border border-amber-100 bg-amber-50/60 px-4 py-3 text-sm leading-relaxed text-neutral-700">
            {{ __('Forgot your password? Enter your email and we will send a link to choose a new password.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-neutral-700" />
                <x-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <button type="submit" class="auth-btn-primary w-full py-3 normal-case tracking-normal">
                {{ __('Email Password Reset Link') }}
            </button>

            <p class="text-center text-sm">
                <a class="font-medium text-[#b87620] transition hover:text-[#8f5a18] focus:outline-none focus-visible:underline" href="{{ route('login') }}">
                    ← {{ __('Back to sign in') }}
                </a>
            </p>
        </form>
    </x-authentication-card>
</x-guest-layout>
