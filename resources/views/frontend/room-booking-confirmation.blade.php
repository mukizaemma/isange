@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Almost done',
    'subtitle' => 'Your booking request is saved. Send it to the hotel with the channel you prefer.',
    'imageUrl' => null,
])

<section class="ma-room-booking ma-room-booking--confirmation py-100 rpy-70 bg-white rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (session('error'))
                    <div class="alert alert-warning">{{ session('error') }}</div>
                @endif

                <div class="ma-room-booking__summary border rounded-3 p-4 p-md-4 bg-light shadow-sm mb-4">
                    <h3 class="h6 text-uppercase text-muted mb-3">Your request</h3>
                    <dl class="row mb-0 ma-room-booking__dl">
                        <dt class="col-sm-4">Guest</dt>
                        <dd class="col-sm-8">{{ $booking->guest_name }}</dd>
                        <dt class="col-sm-4">Stay</dt>
                        <dd class="col-sm-8">
                            {{ $booking->check_in->format('D j M Y') }}
                            <span class="text-muted">→</span>
                            {{ $booking->check_out->format('D j M Y') }}
                        </dd>
                        @if ($booking->room)
                            <dt class="col-sm-4">Room</dt>
                            <dd class="col-sm-8">{{ $booking->room->roomName }}</dd>
                        @endif
                        <dt class="col-sm-4">Airport</dt>
                        <dd class="col-sm-8">
                            @if ($booking->airport_pickup || $booking->airport_dropoff)
                                @if ($booking->airport_pickup) Pickup @endif
                                @if ($booking->airport_pickup && $booking->airport_dropoff) · @endif
                                @if ($booking->airport_dropoff) Drop-off @endif
                            @else
                                <span class="text-muted">None selected</span>
                            @endif
                        </dd>
                    </dl>
                </div>

                <div class="ma-room-booking__next border rounded-3 p-4 p-md-4 bg-white shadow-sm">
                    <p class="mb-4">Choose <strong>WhatsApp</strong> for a quick chat with the team, or <strong>Email</strong> to open your mail app with the same details pre-filled.</p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a class="theme-btn text-center flex-grow-1 flex-sm-grow-0" href="{{ route('room.booking.whatsapp', $booking->public_id) }}"><i class="fab fa-whatsapp me-2"></i> Send via WhatsApp</a>
                        <a class="theme-btn style-three text-center flex-grow-1 flex-sm-grow-0" href="{{ route('room.booking.email', $booking->public_id) }}"><i class="far fa-envelope me-2"></i> Send via email</a>
                    </div>
                    <hr class="my-4">
                    <p class="small text-muted mb-0">Reference: <code class="ma-room-booking__ref">{{ $booking->public_id }}</code></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
