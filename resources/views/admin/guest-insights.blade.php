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
                    <div class="card-header">Recent room booking requests (stored message)</div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>When</th>
                                    <th>Guest</th>
                                    <th>Stay</th>
                                    <th>Fulfillment</th>
                                    <th>Done via</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookingRequests as $b)
                                    <tr>
                                        <td class="text-nowrap">{{ $b->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $b->guest_name }}<br><small class="text-muted">{{ $b->guest_email }}</small></td>
                                        <td>{{ $b->check_in->format('Y-m-d') }} → {{ $b->check_out->format('Y-m-d') }}<br><small class="text-muted">{{ $b->room?->roomName ?? 'Any' }}</small></td>
                                        <td><code>{{ $b->fulfillment_choice }}</code></td>
                                        <td>{{ $b->completed_channel ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted">No submissions yet.</td></tr>
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
