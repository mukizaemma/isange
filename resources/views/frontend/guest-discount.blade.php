@extends('layouts.frontbase')

@section('content')
@include('frontend.includes.page-header', ['pageKey' => 'booking', 'title' => 'Unlock your booking discount'])

<section class="py-80 rpy-60 bg-light">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="ma-checkout-card h-100">
                    <div class="ma-checkout-card__body p-4 p-md-5">
                        <span class="isange-section__eyebrow">New guest</span>
                        <h2>Create your guest account</h2>
                        <p>Your booking cart will remain saved. Confirm your email with a 4-digit code to activate the room discounts.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                        @endif
                        @if (session('error'))<div class="alert alert-warning">{{ session('error') }}</div>@endif

                        <form method="post" action="{{ route('guest.discount.register') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="discount-name">Full name</label>
                                <input class="form-control" id="discount-name" name="name" value="{{ old('name') }}" required autocomplete="name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="discount-email">Email</label>
                                <input class="form-control" id="discount-email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="discount-password">Password</label>
                                    <input class="form-control" id="discount-password" name="password" type="password" required autocomplete="new-password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="discount-password-confirmation">Confirm password</label>
                                    <input class="form-control" id="discount-password-confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="form-check mt-4">
                                <input type="hidden" name="marketing_opt_in" value="0">
                                <input class="form-check-input" id="marketing-opt-in" name="marketing_opt_in" type="checkbox" value="1" @checked(old('marketing_opt_in'))>
                                <label class="form-check-label" for="marketing-opt-in">Send me occasional hotel news and offers by email. I can unsubscribe at any time.</label>
                            </div>
                            <button class="theme-btn w-100 mt-4" type="submit">Create Account &amp; Send Code <i class="far fa-angle-right"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="ma-checkout-card h-100">
                    <div class="ma-checkout-card__body p-4 p-md-5">
                        <span class="isange-section__eyebrow">Returning guest</span>
                        <h2>Sign in</h2>
                        <p>Use your existing guest account. We will send a new code if your email is not yet confirmed.</p>
                        <form method="post" action="{{ route('guest.discount.login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="guest-login-email">Email</label>
                                <input class="form-control" id="guest-login-email" name="email" type="email" required autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="guest-login-password">Password</label>
                                <input class="form-control" id="guest-login-password" name="password" type="password" required autocomplete="current-password">
                            </div>
                            <button class="theme-btn style-three w-100" type="submit">Sign In &amp; Continue</button>
                        </form>
                        <a class="d-block text-center mt-4" href="{{ route('booking.checkout') }}">Continue without discount</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
