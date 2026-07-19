@extends('layouts.frontbase')

@section('body_class', 'is-guest-auth-page')

@section('content')
@include('frontend.includes.page-header', ['pageKey' => 'booking', 'title' => 'Confirm your email'])

<section class="isange-guest-auth isange-guest-auth--verify">
    <div class="container">
        <div class="isange-guest-auth__verify mx-auto">
            <div class="isange-guest-auth__verify-icon" aria-hidden="true">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <span class="isange-section__eyebrow">Almost there</span>
            <h2>Enter your 4-digit code</h2>
            <p>We sent a one-time code to <strong>{{ auth()->user()->email }}</strong>. It expires in 10 minutes.</p>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-warning">{{ session('error') }}</div>
            @endif

            <form method="post" action="{{ route('guest.discount.verify.store') }}" class="isange-guest-auth__otp-form">
                @csrf
                <label class="visually-hidden" for="otp-code">4-digit code</label>
                <input
                    class="isange-guest-auth__otp-input"
                    id="otp-code"
                    name="code"
                    inputmode="numeric"
                    pattern="[0-9]{4}"
                    maxlength="4"
                    required
                    autofocus
                    autocomplete="one-time-code"
                    placeholder="••••"
                >
                <button class="theme-btn isange-guest-auth__submit" type="submit">
                    Confirm &amp; unlock discount <i class="far fa-angle-right"></i>
                </button>
            </form>

            <form method="post" action="{{ route('guest.discount.resend') }}" class="isange-guest-auth__resend">
                @csrf
                <button type="submit">Send a new code</button>
            </form>
            <a class="isange-guest-auth__skip" href="{{ route('booking.checkout') }}">Continue booking at the regular price</a>
        </div>
    </div>
</section>
@endsection
