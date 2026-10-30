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
                        <li class="breadcrumb-item active">#{{ $booking->public_id }}</li>
                    </ol>

                    @if (session()->has('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session()->has('warning'))
                        <div class="alert alert-warning">{{ session('warning') }}</div>
                    @endif
                    @if (session()->has('error'))
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

                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Reservation #{{ $booking->public_id }}</span>
                                    <a href="{{ route('bookings') }}" class="btn btn-sm btn-outline-secondary">Back to Bookings</a>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered mb-0">
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
                                                @if ($booking->reviewed_at)
                                                    <small class="text-muted ms-2">Reviewed {{ $booking->reviewed_at->format('Y-m-d H:i') }}</small>
                                                @endif
                                                @if ($booking->canceled_at)
                                                    <small class="text-muted ms-2">Canceled {{ $booking->canceled_at->format('Y-m-d H:i') }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Submitted via</th>
                                            <td>{{ \App\Models\GuestBookingRequest::channelLabel($booking->fulfillment_choice) }}</td>
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
                                            <td>{{ $booking->guest_email ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Country</th>
                                            <td>{{ $booking->guest_country ?: '—' }}</td>
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
                                        @if ($booking->admin_message)
                                        <tr>
                                            <th>Last admin message</th>
                                            <td>{{ $booking->admin_message }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card mb-4">
                                <div class="card-header">Update status &amp; notify guest</div>
                                <div class="card-body">
                                    @php
                                        $actor = auth()->user();
                                        $statusChoices = $booking->adminStatusChoices($actor);
                                    @endphp
                                    @if ($booking->isOpen() && $booking->checkInHasStarted() && ! $actor->isSuperAdmin())
                                        <p class="small text-muted">The check-in date has passed, so this reservation can no longer be canceled here. Only the super admin can cancel it after check-in.</p>
                                    @endif
                                    @if ($statusChoices !== [])
                                        <form method="POST" action="{{ route('bookings.updateStatus', $booking->id) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="status" class="form-label">New status</label>
                                                <select name="status" id="status" class="form-select" required>
                                                    <option value="" disabled selected>Choose outcome…</option>
                                                    @foreach ($statusChoices as $value => $label)
                                                        <option value="{{ $value }}" @selected(old('status') === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="admin_message" class="form-label">Message to guest <span class="text-muted fw-normal">(optional)</span></label>
                                                <textarea name="admin_message" id="admin_message" rows="5" class="form-control" placeholder="This note is included in the guest email with the status update.">{{ old('admin_message', $booking->admin_message) }}</textarea>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="hidden" name="notify_guest" value="0">
                                                <input class="form-check-input" type="checkbox" value="1" id="notify_guest" name="notify_guest" @checked(old('notify_guest', true))>
                                                <label class="form-check-label" for="notify_guest">
                                                    Email guest
                                                    @if ($booking->guest_email)
                                                        ({{ $booking->guest_email }})
                                                    @else
                                                        <span class="text-danger">— no email on file</span>
                                                    @endif
                                                </label>
                                            </div>

                                            <button type="submit" class="btn btn-primary text-black"
                                                onclick="return confirm('Apply this status update{{ old('notify_guest', true) && $booking->guest_email ? ' and email the guest' : '' }}?');">
                                                Save &amp; notify
                                            </button>
                                        </form>
                                        @if ($booking->canGuestCancel() && filter_var($booking->guest_email, FILTER_VALIDATE_EMAIL))
                                            <hr>
                                            <h3 class="h6">Ask the guest to cancel</h3>
                                            <p class="small text-muted">Sends the manage link and leaves the reservation unchanged until the guest cancels it.</p>
                                            <form method="POST" action="{{ route('bookings.requestCancel', $booking->id) }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="cancel_request_message" class="form-label">Note to the guest <span class="text-muted fw-normal">(optional)</span></label>
                                                    <textarea name="admin_message" id="cancel_request_message" rows="3" class="form-control" placeholder="Please cancel if you will not be arriving.">{{ old('admin_message') }}</textarea>
                                                </div>
                                                <button type="submit" class="btn btn-outline-dark"
                                                    onclick="return confirm('Email {{ $booking->guest_email }} and ask them to cancel if they are not coming?');">
                                                    Email cancel link
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">This reservation is already closed ({{ \App\Models\GuestBookingRequest::statusLabel($booking->status) }}) and cannot be changed further from here.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>

@endsection
