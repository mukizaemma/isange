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
                                    <th>Cart</th>
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
                                        <td class="small">
                                            @if (is_array($b->cart_items))
                                                {{ count($b->cart_items['rooms'] ?? []) }} room(s),
                                                {{ count($b->cart_items['experiences'] ?? []) }} exp.
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-muted">No submissions yet.</td></tr>
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
                                    <th>Channel</th>
                                    <th>Total USD</th>
                                    <th>Lines</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($diningSubmissions as $d)
                                    <tr>
                                        <td class="text-nowrap">{{ $d->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $d->channel }}</td>
                                        <td>{{ $d->grand_total_usd ?? '—' }}</td>
                                        <td>{{ is_array($d->items_json) ? count($d->items_json) : 0 }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted">No dining sends logged yet.</td></tr>
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
