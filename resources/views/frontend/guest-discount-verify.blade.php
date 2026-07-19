@extends('layouts.frontbase')

@section('content')
@include('frontend.includes.page-header', ['pageKey' => 'booking', 'title' => 'Confirm your email'])

<section class="py-80 rpy-60 bg-light">
    <div class="container">
        <div class="ma-checkout-card mx-auto" style="max-width:580px">
            <div class="ma-checkout-card__body p-4 p-md-5 text-center">
                <span class="isange-direct-discount__icon mx-auto mb-3"><i class="fas fa-envelope"></i></span>
                <h2>Enter your 4-digit code</h2>
                <p>We sent a one-time code to <strong>{{ auth()->user()->email }}</strong>. It expires after 10 minutes.</p>

                @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                @if (session('error'))<div class="alert alert-warning">{{ session('error') }}</div>@endif

                <form method="post" action="{{ route('guest.discount.verify.store') }}">
                    @csrf
                    <label class="visually-hidden" for="otp-code">4-digit code</label>
                    <input class="form-control form-control-lg text-center mx-auto" style="max-width:220px;font-size:2rem;letter-spacing:.6rem" id="otp-code" name="code" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required autofocus autocomplete="one-time-code">
                    <button class="theme-btn w-100 mt-4" type="submit">Confirm &amp; Unlock Discount</button>
                </form>

                <form method="post" action="{{ route('guest.discount.resend') }}" class="mt-3">
                    @csrf
                    <button class="btn btn-link text-success" type="submit">Send a new code</button>
                </form>
                <a class="d-block mt-2" href="{{ route('booking.checkout') }}">Continue booking at the regular price</a>
            </div>
        </div>
    </div>
</section>
@endsection
