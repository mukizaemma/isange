@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => session('email_sent') || $booking->completed_channel === 'email' ? 'Request sent' : 'Email delivery',
    'subtitle' => session('email_sent') || $booking->completed_channel === 'email'
        ? 'We emailed your booking request to the hotel team.'
        : 'We could not deliver your request by email automatically.',
    'imageUrl' => null,
])

<section class="py-100 rpy-70 bg-white rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                @if (session('error'))
                    <div class="alert alert-warning text-start">{{ session('error') }}</div>
                @endif

                @if (session('email_sent') || $booking->completed_channel === 'email')
                    <div class="alert alert-success text-start">
                        <strong>Thank you!</strong> Your stay request was sent to <strong>{{ $hotelEmail }}</strong>.
                        The hotel will reply to the email address you provided.
                    </div>
                    <p class="text-muted mb-0">Reference: <code>{{ $booking->public_id }}</code></p>
                @else
                    <p class="mb-4">Please email the hotel directly at <a href="mailto:{{ $hotelEmail }}">{{ $hotelEmail }}</a> and include your reference <code>{{ $booking->public_id }}</code>.</p>
                @endif

                <a class="theme-btn mt-4" href="{{ route('home') }}">Back to home</a>
            </div>
        </div>
    </div>
</section>

@include('frontend.includes.stay-cart-clear-after-booking', ['booking' => $booking ?? null])
@endsection
