@extends('layouts.adminbase')

@section('title', 'Guest insights')

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
                <h1 class="mt-4">Guest insights</h1>
                <p class="text-muted">Aggregated interaction events (no PII) plus stored booking and dining submissions.</p>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('warning'))
                    <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card mb-4">
                    <div class="card-header">Event totals (all time)</div>
                    <div class="card-body">
                        @if ($eventTotals->isEmpty())
                            <p class="text-muted mb-0">No events recorded yet.</p>
                        @else
                            <table class="table table-sm">
                                <thead><tr><th>Event</th><th class="text-end">Count</th></tr></thead>
                                <tbody>
                                    @foreach ($eventTotals as $row)
                                        <tr>
                                            <td><code>{{ $row->event_key }}</code></td>
                                            <td class="text-end">{{ number_format($row->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Reservation payment preferences (all time)</div>
                    <div class="card-body">
                        @if ($paymentMethodTotals->isEmpty())
                            <p class="text-muted mb-0">No payment method data yet.</p>
                        @else
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Method</th><th class="text-end">Bookings</th></tr></thead>
                                <tbody>
                                    @foreach ($paymentMethodTotals as $row)
                                        <tr>
                                            <td><code>{{ $row->method }}</code></td>
                                            <td class="text-end">{{ number_format($row->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Monthly payment method report</div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Month</th><th>Payment method</th><th class="text-end">Count</th></tr></thead>
                            <tbody>
                                @forelse ($monthlyPaymentReport as $row)
                                    <tr>
                                        <td>{{ $row->month }}</td>
                                        <td><code>{{ $row->payment_method }}</code></td>
                                        <td class="text-end">{{ $row->total }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">No data yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Recent room booking requests (stored message)</div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>When</th>
                                    <th>Guest</th>
                                    <th>Stay</th>
                                    <th>Payment</th>
                                    <th>Channel</th>
                                    <th>Status</th>
                                    <th>Cart</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookingRequests as $b)
                                    <tr>
                                        <td class="text-nowrap">{{ $b->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $b->guest_name }}<br><small class="text-muted">{{ $b->guest_email }}</small></td>
                                        <td>{{ $b->check_in->format('Y-m-d') }} → {{ $b->check_out->format('Y-m-d') }}<br><small class="text-muted">{{ $b->room?->roomName ?? 'Any' }}</small></td>
                                        <td><code>{{ $b->payment_method ?? '—' }}</code></td>
                                        <td><code>{{ $b->fulfillment_choice }}</code>@if($b->completed_channel)<br><small>{{ $b->completed_channel }}</small>@endif</td>
                                        <td>
                                            <span class="badge {{ \App\Models\GuestBookingRequest::statusBadgeClass($b->status) }}">
                                                {{ \App\Models\GuestBookingRequest::statusLabel($b->status) }}
                                            </span>
                                            @if ($b->reviewed_at)
                                                <br><small class="text-muted">{{ $b->reviewed_at->format('Y-m-d H:i') }}</small>
                                            @endif
                                        </td>
                                        <td class="small">
                                            @if (is_array($b->cart_items))
                                                {{ count($b->cart_items['rooms'] ?? []) }} room(s),
                                                {{ count($b->cart_items['experiences'] ?? []) }} exp.
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if ($b->canBeReviewed())
                                                <form method="POST" action="{{ route('guestInsights.booking.status', $b->public_id) }}" class="d-flex gap-1 align-items-center" data-channel="{{ $b->fulfillment_choice }}" data-guest-email="{{ $b->guest_email }}" onsubmit="return confirmBookingStatus(this);">
                                                    @csrf
                                                    <select name="status" class="form-select form-select-sm" style="min-width: 11rem;" required>
                                                        <option value="" selected disabled>Choose outcome…</option>
                                                        <option value="confirmed">Confirm</option>
                                                        <option value="unfortunate">Fully booked</option>
                                                        <option value="rejected">Reject (unclear)</option>
                                                        <option value="no_show">No show</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                                </form>
                                            @elseif ($b->canBeMarkedNoShow())
                                                <form method="POST" action="{{ route('guestInsights.booking.status', $b->public_id) }}" class="d-inline" onsubmit="return confirm('Mark this reservation as no-show{{ $b->fulfillment_choice === 'email' ? ' and notify '.$b->guest_email : '' }}?');">
                                                    @csrf
                                                    <input type="hidden" name="status" value="no_show">
                                                    <button type="submit" class="btn btn-sm btn-outline-dark">Mark no show</button>
                                                </form>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-muted">No submissions yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Recent dining sends (WhatsApp / email)</div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>When</th>
                                    <th>Guest</th>
                                    <th>Channel</th>
                                    <th>Total</th>
                                    <th>Lines</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($diningSubmissions as $d)
                                    <tr>
                                        <td class="text-nowrap">{{ $d->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            {{ $d->guest_name ?: '—' }}
                                            @if ($d->guest_phone)<br><small class="text-muted">{{ $d->guest_phone }}</small>@endif
                                            @if ($d->guest_email)<br><small class="text-muted">{{ $d->guest_email }}</small>@endif
                                            @if ($d->special_requests)<br><small class="text-muted" title="{{ $d->special_requests }}">Note: {{ \Illuminate\Support\Str::limit($d->special_requests, 40) }}</small>@endif
                                        </td>
                                        <td>{{ $d->channel }}</td>
                                        <td class="text-nowrap">
                                            ${{ $d->grand_total_usd ?? '—' }}
                                            @if ($d->grand_total_rwf)<br><small class="text-muted">{{ number_format((int) $d->grand_total_rwf) }} RWF</small>@endif
                                        </td>
                                        <td>{{ is_array($d->items_json) ? count($d->items_json) : 0 }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted">No dining sends logged yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@section('scripts')
<script>
function confirmBookingStatus(form) {
    var select = form.querySelector('select[name="status"]');
    if (!select || !select.value) {
        alert('Choose an outcome first.');
        return false;
    }
    var labels = {
        confirmed: 'confirm this reservation',
        unfortunate: 'mark as fully booked',
        rejected: 'reject this reservation (unclear details)',
        no_show: 'mark as no-show'
    };
    var channel = form.getAttribute('data-channel') || '';
    var guestEmail = form.getAttribute('data-guest-email') || '';
    var action = labels[select.value] || 'update this reservation';
    var emailNote = (channel === 'email' && guestEmail) ? ' and send an email to ' + guestEmail : '';
    return confirm('Are you sure you want to ' + action + emailNote + '?');
}
</script>
@endsection
@endsection
