@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Book and pay directly',
    'subtitle' => 'Secure online payment is coming soon.',
    'imageUrl' => null,
])

<section class="py-100 rpy-70 bg-white rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="border rounded-3 p-4 p-md-5 bg-light text-center">
                    <span class="badge bg-warning text-dark mb-3">Coming soon</span>
                    <h2 class="h4 mb-3">Direct card payment</h2>
                    @if (! empty($room))
                        <p class="mb-3">You selected <strong>{{ $room->roomName }}</strong>. When payment is live, you will complete your booking and pay securely on this page.</p>
                    @else
                        <p class="mb-3">When payment is live, you will complete your booking and pay securely on this page.</p>
                    @endif
                    <p class="small text-muted mb-4">Until then, book at our discounted rate through WhatsApp or email, or use Booking.com or Expedia.</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ route('room.booking', array_filter(['room' => $room->slug ?? null])) }}" class="theme-btn style-three">Other booking options</a>
                        <a href="{{ route('rooms') }}" class="theme-btn">View accommodation</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
