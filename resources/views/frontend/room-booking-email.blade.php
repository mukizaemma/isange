@extends('layouts.frontbase')

@section('content')

@php
    $emailDelivered = session('email_sent') || $booking->completed_channel === 'email';
@endphp

@include('frontend.includes.page-header', [
    'title' => $emailDelivered ? 'Request received' : 'Email delivery',
    'subtitle' => $emailDelivered
        ? 'We received your booking request and sent you a confirmation email.'
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
                @if (session('warning'))
                    <div class="alert alert-warning text-start">{{ session('warning') }}</div>
                @endif

                @if ($emailDelivered)
                    <div class="alert alert-success text-start">
                        <strong>Thank you!</strong> Your stay request has been received.
                        A confirmation email was sent to <strong>{{ $booking->guest_email }}</strong>.
                        Our team will review your reservation and send a final confirmation once approved.
                    </div>
                    <p class="text-muted mb-0">Reference: <code>{{ $booking->public_id }}</code></p>
                @else
                    <div class="border rounded-3 p-4 p-md-5 bg-light shadow-sm text-start mb-4">
                        <p class="mb-3">Your booking is saved with reference <code>{{ $booking->public_id }}</code>. Choose how to send it to the hotel:</p>

                        @if (! empty($hotelWhatsappReady))
                            <div class="d-flex flex-column flex-sm-row gap-3 mb-4">
                                <a class="theme-btn text-center flex-grow-1" href="{{ route('room.booking.whatsapp', $booking->public_id) }}">
                                    <i class="fab fa-whatsapp me-2"></i> Send via WhatsApp
                                </a>
                                <a class="theme-btn style-three text-center flex-grow-1" href="{{ route('room.booking.email', $booking->public_id) }}">
                                    <i class="far fa-envelope me-2"></i> Retry email
                                </a>
                            </div>
                            <p class="small text-muted mb-0">WhatsApp opens with your booking details pre-filled — tap send to deliver your request.</p>
                        @else
                            <p class="mb-3">Please email the hotel directly at <a href="mailto:{{ $hotelEmail }}?subject={{ rawurlencode('Booking request '.$booking->public_id) }}">{{ $hotelEmail }}</a> and include your reference <code>{{ $booking->public_id }}</code>.</p>
                            <a class="theme-btn style-three" href="{{ route('room.booking.email', $booking->public_id) }}">
                                <i class="far fa-envelope me-2"></i> Retry automatic email
                            </a>
                        @endif
                    </div>
                @endif

                <a class="theme-btn mt-2{{ $emailDelivered ? ' mt-4' : '' }}" href="{{ route('home') }}">Back to home</a>
            </div>
        </div>
    </div>
</section>

@include('frontend.includes.stay-cart-clear-after-booking', ['booking' => $booking ?? null])
@endsection
