@extends('layouts.frontbase')

@section('content')
@include('frontend.includes.page-header', ['pageKey' => 'booking', 'title' => 'My bookings'])

<section class="py-80 rpy-60">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div><span class="isange-section__eyebrow">Guest account</span><h2 class="mb-0">My bookings</h2></div>
            <a class="theme-btn" href="{{ route('booking.checkout') }}">Book another stay</a>
        </div>
        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table align-middle mb-0">
                <thead><tr><th>Reference</th><th>Stay</th><th>Room</th><th>Status</th><th>Total</th></tr></thead>
                <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td>#{{ $booking->public_id }}</td>
                        <td>{{ $booking->check_in?->format('M j, Y') }} – {{ $booking->check_out?->format('M j, Y') }}</td>
                        <td>{{ $booking->room?->roomName ?? 'Multiple rooms' }}</td>
                        <td><span class="badge {{ \App\Models\GuestBookingRequest::statusBadgeClass($booking->status) }}">{{ \App\Models\GuestBookingRequest::statusLabel($booking->status) }}</span></td>
                        <td>{{ $booking->total_usd ? '$'.number_format((float) $booking->total_usd, 2) : '—' }} @if($booking->discount_applied)<span class="badge bg-success">Discounted</span>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No bookings yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $bookings->links() }}</div>
    </div>
</section>
@endsection
