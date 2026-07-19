@extends('layouts.adminbase')

@section('title', 'Guests')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">@include('admin.includes.sidenav')</div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Guests</h1>
                <p class="text-muted">Verified guest accounts, direct-booking activity, returning guests, and consent-aware email updates.</p>

                @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                @if (session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif
                @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

                <div class="card mb-4">
                    <div class="card-body">
                        <form class="row g-3 align-items-end" method="get">
                            <div class="col-md-4"><label class="form-label" for="from">Bookings from</label><input class="form-control" id="from" type="date" name="from" value="{{ $from }}"></div>
                            <div class="col-md-4"><label class="form-label" for="to">Bookings to</label><input class="form-control" id="to" type="date" name="to" value="{{ $to }}"></div>
                            <div class="col-md-4"><button class="btn btn-primary" type="submit">Apply date range</button> <a class="btn btn-outline-secondary" href="{{ route('admin.guests.index') }}">Reset</a></div>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted">Guest profiles</div><div class="display-6">{{ number_format($guests->total() + $bookingOnlyGuests->count()) }}</div></div></div></div>
                    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted">Returning guests in range</div><div class="display-6">{{ number_format($returningGuests) }}</div><small>At least 2 website bookings</small></div></div></div>
                </div>

                <form method="post" action="{{ route('admin.guests.updates.send') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-envelope me-1"></i> Send guest update</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8"><label class="form-label" for="update-title">Title</label><input class="form-control" id="update-title" name="title" value="{{ old('title') }}" required></div>
                                <div class="col-md-4"><label class="form-label" for="cover-image">Cover image</label><input class="form-control" id="cover-image" type="file" name="cover_image" accept="image/*"></div>
                                <div class="col-12"><label class="form-label" for="update-description">Description</label><textarea class="form-control" id="update-description" name="description" rows="6" required>{{ old('description') }}</textarea></div>
                                <div class="col-md-4">
                                    <label class="form-label" for="recipient-mode">Recipients</label>
                                    <select class="form-select" id="recipient-mode" name="recipient_mode" required>
                                        <option value="selected">Selected guests below</option>
                                        <option value="date_range" @selected(old('recipient_mode') === 'date_range')>Guests who booked in date range</option>
                                    </select>
                                </div>
                                <div class="col-md-3"><label class="form-label" for="booking-from">From</label><input class="form-control" id="booking-from" type="date" name="booking_from" value="{{ old('booking_from', $from) }}"></div>
                                <div class="col-md-3"><label class="form-label" for="booking-to">To</label><input class="form-control" id="booking-to" type="date" name="booking_to" value="{{ old('booking_to', $to) }}"></div>
                                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-success w-100" type="submit">Send update</button></div>
                            </div>
                            <p class="small text-muted mt-3 mb-0">Only verified guests who explicitly opted in will receive marketing updates.</p>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between"><span>Guest accounts — newest first</span><label><input type="checkbox" id="select-all-guests"> Select page</label></div>
                        <div class="card-body table-responsive">
                            <table class="table table-hover align-middle">
                                <thead><tr><th></th><th>Guest</th><th>Joined</th><th>Verified</th><th>Updates consent</th><th>Discount visits</th><th class="text-end">Bookings in range</th></tr></thead>
                                <tbody>
                                @forelse ($guests as $guest)
                                    <tr>
                                        <td><input class="guest-select" type="checkbox" name="guest_ids[]" value="{{ $guest->id }}"></td>
                                        <td><strong>{{ $guest->name }}</strong><br><a href="mailto:{{ $guest->email }}">{{ $guest->email }}</a></td>
                                        <td>{{ $guest->created_at->format('M j, Y') }}</td>
                                        <td>{!! $guest->email_verified_at ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-secondary">Pending</span>' !!}</td>
                                        <td>{!! $guest->marketing_opt_in ? '<span class="badge bg-success">Opted in</span>' : '<span class="badge bg-light text-dark">Not opted in</span>' !!}</td>
                                        <td>
                                            <strong>{{ number_format($guest->discount_unlock_count) }}</strong>
                                            @if($guest->discount_unlock_count >= 2) <span class="badge bg-info text-dark">Returned</span>@endif
                                            @if($guest->last_discount_unlocked_at)<br><small class="text-muted">{{ $guest->last_discount_unlocked_at->format('M j, Y H:i') }}</small>@endif
                                        </td>
                                        <td class="text-end"><strong>{{ $guest->booking_count }}</strong>@if($guest->booking_count >= 2) <span class="badge bg-info text-dark">Returning</span>@endif</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">No guest accounts found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            {{ $guests->links() }}
                        </div>
                    </div>
                </form>

                <div class="card mb-4">
                    <div class="card-header">Booking-only guests</div>
                    <div class="card-body">
                        <p class="small text-muted">Guests who booked without creating an account are included in reporting, but cannot receive marketing updates because no opt-in was recorded.</p>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead><tr><th>Guest</th><th>Latest booking</th><th class="text-end">Bookings in range</th></tr></thead>
                                <tbody>
                                @forelse($bookingOnlyGuests as $guest)
                                    <tr>
                                        <td><strong>{{ $guest->name ?: 'Guest' }}</strong><br><a href="mailto:{{ $guest->email }}">{{ $guest->email }}</a></td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($guest->latest_booking)->format('M j, Y') }}</td>
                                        <td class="text-end"><strong>{{ $guest->booking_count }}</strong>@if($guest->booking_count >= 2) <span class="badge bg-info text-dark">Returning</span>@endif</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No booking-only guests in this range.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Recent email updates</div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Title</th><th>Sent</th><th>Recipients</th><th>Delivered</th></tr></thead>
                            <tbody>
                            @forelse($updates as $update)
                                <tr><td>{{ $update->title }}</td><td>{{ $update->sent_at?->format('M j, Y H:i') ?? '—' }}</td><td>{{ $update->recipient_count }}</td><td>{{ $update->sent_count }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">No updates sent yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('select-all-guests')?.addEventListener('change', function () {
    document.querySelectorAll('.guest-select').forEach((box) => box.checked = this.checked);
});
</script>
@endsection
