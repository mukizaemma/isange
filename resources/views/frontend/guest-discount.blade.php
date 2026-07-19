@extends('layouts.frontbase')

@section('body_class', 'is-guest-auth-page')

@section('content')
@include('frontend.includes.page-header', ['pageKey' => 'booking', 'title' => 'Unlock your booking discount'])

<section class="isange-guest-auth">
    <div class="container">
        <div class="isange-guest-auth__intro text-center mx-auto">
            <span class="isange-section__eyebrow">Direct booking benefit</span>
            <h2>Unlock discounted room rates</h2>
            <p>Create a guest account, confirm your email with a 4-digit code, and keep your cart while we apply the savings.</p>
            <ul class="isange-guest-auth__perks" aria-label="What you unlock">
                <li><i class="fas fa-tag" aria-hidden="true"></i> Room discounts activated</li>
                <li><i class="fas fa-shopping-bag" aria-hidden="true"></i> Cart stays saved</li>
                <li><i class="fas fa-shield-alt" aria-hidden="true"></i> Secure email OTP</li>
            </ul>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger isange-guest-auth__alert mx-auto">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-warning isange-guest-auth__alert mx-auto">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success isange-guest-auth__alert mx-auto">{{ session('success') }}</div>
        @endif

        <div class="isange-guest-auth__grid">
            <article class="isange-guest-auth__panel isange-guest-auth__panel--primary">
                <header class="isange-guest-auth__panel-head">
                    <span class="isange-guest-auth__badge">New guest</span>
                    <h3>Create your account</h3>
                    <p>We’ll email a one-time code so you can unlock the discount and continue booking.</p>
                </header>

                <form method="post" action="{{ route('guest.discount.register') }}" class="isange-guest-auth__form">
                    @csrf
                    <div class="isange-guest-auth__field">
                        <label for="discount-name">Full name</label>
                        <input id="discount-name" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Jane Guest">
                    </div>
                    <div class="isange-guest-auth__field">
                        <label for="discount-email">Email</label>
                        <input id="discount-email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@email.com">
                    </div>
                    <div class="isange-guest-auth__row">
                        <div class="isange-guest-auth__field">
                            <label for="discount-password">Password</label>
                            <input id="discount-password" name="password" type="password" required autocomplete="new-password" placeholder="At least 8 characters">
                        </div>
                        <div class="isange-guest-auth__field">
                            <label for="discount-password-confirmation">Confirm password</label>
                            <input id="discount-password-confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Repeat password">
                        </div>
                    </div>

                    <label class="isange-guest-auth__consent" for="marketing-opt-in">
                        <input type="hidden" name="marketing_opt_in" value="0">
                        <input id="marketing-opt-in" name="marketing_opt_in" type="checkbox" value="1" @checked(old('marketing_opt_in'))>
                        <span>Send me occasional hotel news and offers by email. I can unsubscribe at any time.</span>
                    </label>

                    <button class="theme-btn isange-guest-auth__submit" type="submit">
                        Create account &amp; send code <i class="far fa-angle-right"></i>
                    </button>
                </form>
            </article>

            <article class="isange-guest-auth__panel isange-guest-auth__panel--secondary">
                <header class="isange-guest-auth__panel-head">
                    <span class="isange-guest-auth__badge isange-guest-auth__badge--green">Returning guest</span>
                    <h3>Welcome back</h3>
                    <p>Sign in with your guest account. If your email is not confirmed yet, we’ll send a fresh code.</p>
                </header>

                <form method="post" action="{{ route('guest.discount.login') }}" class="isange-guest-auth__form">
                    @csrf
                    <div class="isange-guest-auth__field">
                        <label for="guest-login-email">Email</label>
                        <input id="guest-login-email" name="email" type="email" required autocomplete="email" placeholder="you@email.com">
                    </div>
                    <div class="isange-guest-auth__field">
                        <label for="guest-login-password">Password</label>
                        <input id="guest-login-password" name="password" type="password" required autocomplete="current-password" placeholder="Your password">
                    </div>
                    <button class="theme-btn style-three isange-guest-auth__submit" type="submit">
                        Sign in &amp; continue <i class="far fa-angle-right"></i>
                    </button>
                </form>

                <div class="isange-guest-auth__aside">
                    <p>Prefer to book at the regular rate?</p>
                    <a href="{{ route('booking.checkout') }}">Continue without discount</a>
                </div>
            </article>
        </div>
    </div>
</section>
@endsection
