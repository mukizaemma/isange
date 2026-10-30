@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Manage reservation',
    'subtitle' => 'Reference '.$booking->public_id,
    'imageUrl' => null,
])

<section class="py-80 rpy-60 bg-white reservation-manage">
    <style>
        .reservation-manage select.form-control {
            display: block !important;
            width: 100%;
            min-height: 52px;
            padding: 12px 8px;
            font-size: 18px;
            background: #fff;
        }
        .reservation-manage .nice-select {
            display: none !important;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('warning'))
                    <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="border rounded-3 p-4 bg-light mb-4">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                        <h2 class="h4 mb-0">Reservation #{{ $booking->public_id }}</h2>
                        <span class="badge {{ \App\Models\GuestBookingRequest::statusBadgeClass($booking->status) }}">
                            {{ \App\Models\GuestBookingRequest::statusLabel($booking->status) }}
                        </span>
                    </div>
                    <p class="mb-1"><strong>Guest:</strong> {{ $booking->guest_name }}</p>
                    <p class="mb-1"><strong>Stay:</strong> {{ $booking->check_in?->format('M j, Y') }} – {{ $booking->check_out?->format('M j, Y') }} ({{ $booking->nightsCount() }} night{{ $booking->nightsCount() === 1 ? '' : 's' }})</p>
                    <p class="mb-1"><strong>Guests:</strong> {{ $booking->stayGuestsLabel() }}</p>
                    <p class="mb-1"><strong>Room:</strong> {{ $booking->roomSummary() }}</p>
                    <p class="mb-1"><strong>Estimated total:</strong> {{ $booking->formattedTotalUsd() }}</p>
                    <p class="mb-0"><strong>Special requests:</strong> {{ $booking->additional_requests ?: '—' }}</p>
                </div>

                @if ($booking->canGuestUpdate())
                    <div class="border rounded-3 p-4 mb-4">
                        <h3 class="h5">Update this reservation</h3>
                        <p class="text-muted">Change the dates, number of guests, room, or special requests. The hotel is emailed as soon as you save.</p>
                        @if ($booking->isConfirmed())
                            <p class="small mb-3">Changing the dates, guests, room, or price sends a confirmed reservation back to the hotel for confirmation. A special request on its own does not.</p>
                        @endif
                        @if (count($cartRooms) > 1)
                            <p class="small mb-3">Dates and guest counts apply to every room on this reservation.</p>
                        @endif

                        <form method="POST" action="{{ $updateUrl }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="check_in">Check-in</label>
                                    <input type="date" class="form-control" id="check_in" name="check_in" required value="{{ old('check_in', $booking->check_in?->toDateString()) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="check_out">Check-out</label>
                                    <input type="date" class="form-control" id="check_out" name="check_out" required value="{{ old('check_out', $booking->check_out?->toDateString()) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="adults">Adults</label>
                                    <input type="number" class="form-control" id="adults" name="adults" min="1" max="20" required value="{{ old('adults', $booking->adults ?: 1) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="children">Children</label>
                                    <input type="number" class="form-control" id="children" name="children" min="0" max="20" required value="{{ old('children', $booking->children ?? 0) }}">
                                </div>
                                @if (count($cartRooms) > 1)
                                    @foreach ($cartRooms as $index => $line)
                                        <div class="col-12">
                                            <label class="form-label" for="room_ids_{{ $index }}">Room {{ $index + 1 }}</label>
                                            <select class="form-control" id="room_ids_{{ $index }}" name="room_ids[{{ $index }}]" required>
                                                @foreach ($rooms as $room)
                                                    <option value="{{ $room->id }}" @selected((int) old('room_ids.'.$index, $line['room_id'] ?? 0) === (int) $room->id)>{{ $room->roomName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <label class="form-label" for="room_id">Room</label>
                                        <select class="form-control" id="room_id" name="room_id" required>
                                            @foreach ($rooms as $room)
                                                <option value="{{ $room->id }}" @selected((int) old('room_id', $cartRooms[0]['room_id'] ?? $booking->room_id) === (int) $room->id)>{{ $room->roomName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <label class="form-label" for="additional_requests">Special requests</label>
                                    <textarea class="form-control" id="additional_requests" name="additional_requests" rows="4" maxlength="5000">{{ old('additional_requests', $booking->additional_requests) }}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="theme-btn mt-4">Save changes</button>
                        </form>
                    </div>

                    @if ($booking->canGuestCancel())
                        <div class="border rounded-3 p-4 border-danger">
                            <h3 class="h5">Cancel this reservation</h3>
                            <p class="mb-3">Canceling releases the room and marks the reservation as canceled by you. This stays available until the check-in date.</p>
                            <form method="POST" action="{{ $cancelUrl }}" onsubmit="return confirm('Cancel this reservation?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">Cancel reservation</button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="alert alert-secondary">
                        @if ($booking->isCanceled())
                            This reservation is canceled
                            @if ($booking->canceled_at)
                                ({{ $booking->canceled_at->format('M j, Y H:i') }})
                            @endif
                            and can no longer be changed.
                        @elseif ($booking->checkInHasStarted())
                            Updates and cancellation close on the check-in date. Contact the hotel if you still need to change this stay.
                        @else
                            This reservation is closed and can no longer be changed from this page.
                        @endif
                    </div>
                @endif

                <a class="theme-btn style-three mt-4" href="{{ route('home') }}">Back to home</a>
            </div>
        </div>
    </div>
</section>
@endsection
