@extends('layouts.adminbase')

@section('sidebar')
    @parent
@endsection

@section('content')

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            @include('admin.includes.sidenav')
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="{{ route('bookings') }}">Reservations</a></li>
                        <li class="breadcrumb-item active">Booking details</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Reservation #{{ $booking->public_id }}</span>
                            <a href="{{ route('bookings') }}" class="btn btn-primary text-black">Back to Bookings</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="220">Submitted</th>
                                    <td>{{ $booking->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge {{ \App\Models\GuestBookingRequest::statusBadgeClass($booking->status) }}">
                                            {{ \App\Models\GuestBookingRequest::statusLabel($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Guest name</th>
                                    <td>{{ $booking->guest_name }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $booking->guest_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $booking->guest_email }}</td>
                                </tr>
                                <tr>
                                    <th>Country</th>
                                    <td>{{ $booking->guest_country }}</td>
                                </tr>
                                <tr>
                                    <th>Room</th>
                                    <td>{{ $booking->room->roomName ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Check-in</th>
                                    <td>{{ $booking->check_in?->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Check-out</th>
                                    <td>{{ $booking->check_out?->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Adults / Children</th>
                                    <td>{{ $booking->adults }} / {{ $booking->children }}</td>
                                </tr>
                                <tr>
                                    <th>Payment</th>
                                    <td>{{ $booking->payment_method ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Total (USD)</th>
                                    <td>{{ $booking->total_usd ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Additional request</th>
                                    <td>{{ $booking->additional_requests ?: '—' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>

@endsection
