<x-guest-layout>
    <x-authentication-card :subtitle="__('Staff registration · Martin Aviator Hotel')">
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" class="text-neutral-700" />
                <x-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-neutral-700" />
                <x-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" class="text-neutral-700" />
                <x-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-neutral-700" />
                <x-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="rounded-lg border border-neutral-100 bg-neutral-50/80 p-3">
                    <x-label for="terms">
                        <div class="flex items-start gap-2">
                            <x-checkbox name="terms" id="terms" class="mt-0.5 rounded border-neutral-300 text-[#e69138] focus:ring-[#e69138]" required />

                            <div class="text-sm text-neutral-600">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="font-medium text-[#b87620] underline decoration-[#e69138]/40 hover:text-[#8f5a18]">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="font-medium text-[#b87620] underline decoration-[#e69138]/40 hover:text-[#8f5a18]">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="space-y-4 pt-2">
                <button type="submit" class="auth-btn-primary w-full py-3 normal-case tracking-normal">
                    {{ __('Register') }}
                </button>

                <p class="text-center text-sm text-neutral-600">
                    <a class="font-medium text-[#b87620] transition hover:text-[#8f5a18] focus:outline-none focus-visible:underline" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>
                </p>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
