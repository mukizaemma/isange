@extends('layouts.adminbase')

@section('sidebar')
    @parent
@endsection

@section('content')
@php
    $filters = $filters ?? ['start_date' => $start_date ?? null, 'end_date' => $end_date ?? null, 'channel' => null, 'payment' => null, 'status' => null];
    $summary = $summary ?? [
        'total' => 0, 'pay_at_hotel' => 0, 'pay_directly' => 0, 'card' => 0,
        'whatsapp' => 0, 'email' => 0, 'confirmed' => 0, 'pending' => 0, 'amount_usd' => 0,
    ];
    $filterQuery = array_filter($filters, fn ($value) => $value !== null && $value !== '');
@endphp

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            @include('admin.includes.sidenav')
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 pb-5">
                    <ol class="breadcrumb mb-3 mt-3">
                        <li class="breadcrumb-item text-muted">Website reservations</li>
                        <li class="breadcrumb-item active">Online booking requests</li>
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

                    <div class="isange-resv-stats mb-4">
                        <div class="isange-resv-stat isange-resv-stat--total">
                            <span>Total</span>
                            <strong>{{ $summary['total'] }}</strong>
                        </div>
                        <div class="isange-resv-stat isange-resv-stat--direct">
                            <span>Pay directly</span>
                            <strong>{{ $summary['pay_directly'] }}</strong>
                        </div>
                        <div class="isange-resv-stat isange-resv-stat--hotel">
                            <span>Pay at hotel</span>
                            <strong>{{ $summary['pay_at_hotel'] }}</strong>
                        </div>
                        <div class="isange-resv-stat">
                            <span>Via WhatsApp</span>
                            <strong>{{ $summary['whatsapp'] }}</strong>
                        </div>
                        <div class="isange-resv-stat">
                            <span>Via email</span>
                            <strong>{{ $summary['email'] }}</strong>
                        </div>
                        <div class="isange-resv-stat">
                            <span>Via card</span>
                            <strong>{{ $summary['card'] }}</strong>
                        </div>
                        <div class="isange-resv-stat isange-resv-stat--amount">
                            <span>Total amount</span>
                            <strong>${{ number_format((float) $summary['amount_usd'], 2) }}</strong>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                            <div>
                                <h2 class="h5 mb-0">Online booking requests</h2>
                                <p class="text-muted small mb-0">From the booking wizard</p>
                            </div>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('bookings.print', $filterQuery) }}">
                                <i class="fas fa-print me-1"></i> Print list
                            </a>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('searchBookings') }}" class="row g-2 align-items-end mb-3">
                                <div class="col-6 col-md-2">
                                    <label class="form-label small mb-1" for="filter_channel">Confirmed via</label>
                                    <select class="form-select form-select-sm" id="filter_channel" name="channel">
                                        <option value="">All channels</option>
                                        <option value="whatsapp" @selected(($filters['channel'] ?? '') === 'whatsapp')>WhatsApp</option>
                                        <option value="email" @selected(($filters['channel'] ?? '') === 'email')>Email</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small mb-1" for="filter_payment">Payment</label>
                                    <select class="form-select form-select-sm" id="filter_payment" name="payment">
                                        <option value="">All payments</option>
                                        <option value="pay_at_hotel" @selected(($filters['payment'] ?? '') === 'pay_at_hotel')>Pay at hotel</option>
                                        <option value="pay_directly" @selected(($filters['payment'] ?? '') === 'pay_directly')>Pay directly</option>
                                        <option value="card" @selected(($filters['payment'] ?? '') === 'card')>Via card</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small mb-1" for="filter_status">Status</label>
                                    <select class="form-select form-select-sm" id="filter_status" name="status">
                                        <option value="">All statuses</option>
                                        <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
                                        <option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>Confirmed</option>
                                        <option value="unfortunate" @selected(($filters['status'] ?? '') === 'unfortunate')>Fully booked</option>
                                        <option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>Rejected</option>
                                        <option value="no_show" @selected(($filters['status'] ?? '') === 'no_show')>No show</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small mb-1" for="start_date">From date</label>
                                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ $filters['start_date'] ?? '' }}">
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small mb-1" for="end_date">To date</label>
                                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ $filters['end_date'] ?? '' }}">
                                </div>
                                <div class="col-6 col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                    <a href="{{ route('bookings') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                                </div>
                            </form>

                            <p class="small text-muted mb-3">
                                @if (($filters['start_date'] ?? null) || ($filters['end_date'] ?? null))
                                    Showing filtered results
                                    @if ($filters['start_date'] ?? null) from {{ $filters['start_date'] }} @endif
                                    @if ($filters['end_date'] ?? null) to {{ $filters['end_date'] }} @endif
                                    —
                                @else
                                    Showing all reservations —
                                @endif
                                <strong>{{ $summary['total'] }}</strong> request{{ $summary['total'] === 1 ? '' : 's' }},
                                <strong class="text-success">${{ number_format((float) $summary['amount_usd'], 2) }}</strong> total
                            </p>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle isange-resv-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Guest</th>
                                            <th>Stay</th>
                                            <th>Room</th>
                                            <th>Total</th>
                                            <th>Payment</th>
                                            <th>Confirmed via</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($bookings as $rs)
                                            @php
                                                $nights = $rs->nightsCount();
                                            @endphp
                                            <tr>
                                                <td class="text-nowrap">{{ $rs->created_at?->format('d M Y H:i') }}</td>
                                                <td>
                                                    <strong>{{ $rs->guest_name }}</strong>
                                                    @if ($rs->guest_email)
                                                        <div class="small text-muted">{{ $rs->guest_email }}</div>
                                                    @endif
                                                    @if ($rs->guest_phone)
                                                        <div class="small text-muted">{{ $rs->guest_phone }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($rs->check_in && $rs->check_out)
                                                        {{ $rs->check_in->format('d M Y') }} → {{ $rs->check_out->format('d M Y') }}
                                                        <div class="small text-muted">
                                                            {{ $nights }} night{{ $nights === 1 ? '' : 's' }}, {{ $rs->stayGuestsLabel() }}
                                                        </div>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $rs->roomSummary() }}</td>
                                                <td class="text-nowrap fw-semibold text-success">{{ $rs->formattedTotalUsd() }}</td>
                                                <td>
                                                    <span class="badge bg-success">{{ \App\Models\GuestBookingRequest::paymentLabel($rs->payment_method) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ \App\Models\GuestBookingRequest::channelLabel($rs->fulfillment_choice) }}</span>
                                                </td>
                                                <td style="min-width: 9.5rem;">
                                                    @if ($rs->canBeReviewed() || $rs->canBeMarkedNoShow())
                                                        <form method="POST" action="{{ route('bookings.updateStatus', $rs->id) }}">
                                                            @csrf
                                                            <select name="status" class="form-select form-select-sm" onchange="if(!this.value || this.value === '{{ $rs->status }}'){ this.value='{{ $rs->status }}'; return; } if(confirm('Update this reservation to ' + this.options[this.selectedIndex].text + '?')){ this.form.submit(); } else { this.value='{{ $rs->status }}'; }">
                                                                <option value="{{ $rs->status }}" selected>{{ \App\Models\GuestBookingRequest::statusLabel($rs->status) }}</option>
                                                                @if ($rs->canBeReviewed())
                                                                    <option value="confirmed">Confirmed</option>
                                                                    <option value="unfortunate">Fully booked</option>
                                                                    <option value="rejected">Rejected</option>
                                                                    <option value="no_show">No show</option>
                                                                @elseif ($rs->canBeMarkedNoShow())
                                                                    <option value="no_show">No show</option>
                                                                @endif
                                                            </select>
                                                        </form>
                                                    @else
                                                        <span class="badge {{ \App\Models\GuestBookingRequest::statusBadgeClass($rs->status) }}">
                                                            {{ \App\Models\GuestBookingRequest::statusLabel($rs->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-nowrap">
                                                    <a href="{{ route('viewBooking', $rs->id) }}" class="small me-2">View</a>
                                                    <a href="{{ route('destroyBooking', $rs->id) }}" class="small text-danger" onclick="return confirm('Remove this reservation?')">Remove</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-muted text-center py-4">No reservations found for this period.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($bookings->isNotEmpty())
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-end">Range total</th>
                                                <th class="text-success">${{ number_format((float) $summary['amount_usd'], 2) }}</th>
                                                <th colspan="4"></th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>

<style>
.isange-resv-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
}
.isange-resv-stat {
    background: #fff;
    border: 1px solid #e9ecef;
    border-top: 3px solid #ced4da;
    border-radius: 6px;
    padding: 12px 14px;
}
.isange-resv-stat span {
    display: block;
    color: #6c757d;
    font-size: 12px;
}
.isange-resv-stat strong {
    display: block;
    font-size: 1.6rem;
    line-height: 1.2;
    margin-top: 2px;
}
.isange-resv-stat--total { border-top-color: #212529; }
.isange-resv-stat--direct { border-top-color: #f0ad4e; }
.isange-resv-stat--hotel { border-top-color: #198754; }
.isange-resv-stat--amount { border-top-color: #0d6efd; background: #f8fbff; }
.isange-resv-table th { white-space: nowrap; font-size: 13px; color: #6c757d; font-weight: 600; }
.isange-resv-table td { font-size: 14px; }
</style>
@endsection
