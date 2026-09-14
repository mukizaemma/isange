{{-- Hotel-wide nights when the direct-booking discount is closed (full rate). --}}
@php
    $month = $discountCalendarMonth ?? now()->startOfMonth();
    $closed = collect($discountClosedDates ?? []);
    $occupancy = $discountOccupancyByNight ?? [];
    $prevMonth = $month->copy()->subMonth()->format('Y-m');
    $nextMonth = $month->copy()->addMonth()->format('Y-m');
    $pad = (int) $month->dayOfWeek;
    $daysInMonth = $month->daysInMonth;
@endphp
<div class="card mb-4 border-secondary">
    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
        <strong><i class="fas fa-calendar-alt me-1"></i> Discount nights</strong>
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('getRooms', ['month' => $prevMonth]) }}">&larr; {{ $month->copy()->subMonth()->format('M Y') }}</a>
            <span class="fw-semibold">{{ $month->format('F Y') }}</span>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('getRooms', ['month' => $nextMonth]) }}">{{ $month->copy()->addMonth()->format('M Y') }} &rarr;</a>
        </div>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            The discount amount is set above. Use this calendar to turn the hotel-wide promo <strong>off</strong> on busy nights.
            If a guest’s stay includes any closed night, the whole stay is charged at full rate.
            Numbers on each day are pending + confirmed bookings overlapping that night.
        </p>

        <div class="table-responsive mb-3">
            <table class="table table-bordered table-sm text-center mb-0 discount-night-cal">
                <thead>
                    <tr>
                        <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @for ($i = 0; $i < $pad; $i++)
                            <td class="bg-light"></td>
                        @endfor
                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = $month->copy()->day($day)->toDateString();
                                $isClosed = $closed->contains($date);
                                $booked = (int) ($occupancy[$date] ?? 0);
                            @endphp
                            @if (($pad + $day - 1) % 7 === 0 && $day !== 1)
                                </tr><tr>
                            @endif
                            <td class="{{ $isClosed ? 'table-secondary' : 'table-success' }} p-1">
                                <form method="POST" action="{{ route('rooms.discountNights') }}" class="m-0">
                                    @csrf
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
                                    <button type="submit" class="btn btn-sm w-100 {{ $isClosed ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                        title="{{ $isClosed ? 'Closed (full rate) — click to open discount' : 'Open (promo can apply) — click to close' }}">
                                        <strong>{{ $day }}</strong>
                                        <span class="d-block small">{{ $isClosed ? 'Off' : 'On' }}</span>
                                        @if ($booked > 0)
                                            <span class="d-block small text-muted">{{ $booked }} bk</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                        @endfor
                        @php $tail = (7 - (($pad + $daysInMonth) % 7)) % 7; @endphp
                        @for ($i = 0; $i < $tail; $i++)
                            <td class="bg-light"></td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('rooms.discountNights') }}" class="row g-2 align-items-end">
            @csrf
            <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
            <div class="col-md-3">
                <label for="range_from" class="form-label">From</label>
                <input type="date" class="form-control @error('range_from') is-invalid @enderror" id="range_from" name="range_from" value="{{ old('range_from') }}" required>
                @error('range_from')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3">
                <label for="range_to" class="form-label">To</label>
                <input type="date" class="form-control @error('range_to') is-invalid @enderror" id="range_to" name="range_to" value="{{ old('range_to') }}" required>
                @error('range_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 d-flex flex-wrap gap-2">
                <button type="submit" name="action" value="close_range" class="btn btn-outline-secondary">Close range (full rate)</button>
                <button type="submit" name="action" value="open_range" class="btn btn-outline-success">Open range (promo on)</button>
            </div>
        </form>
    </div>
</div>
