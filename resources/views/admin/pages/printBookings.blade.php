@extends('layouts.adminbase')

@section('title', 'Tours')

@section('content')
<head>
    <title>Print Booking List</title>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .card, .card * {
                visibility: visible;
            }
            .card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                box-shadow: none;
            }
            .card-header button {
                display: none;
            }
        }

        body {
            font-family: Arial, sans-serif;
        }

        .card {
            margin: 20px;
            padding: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                @php
                $data = App\Models\Setting::first()
                @endphp
                <div class="row" style="">
                    <div class="col-md-6">
                        <img src="{{ asset('storage/images') . $data->logo }}" alt="" style="max-height:200px;">
                    </div>
                </div>
                <button onclick="printPage()" class="btn btn-primary">Print</button>
            </div>
            <div class="card-body">
                @if (($start_date ?? null) || ($end_date ?? null))
                    <div class="alert alert-info mt-2">
                        Reservations
                        @if ($start_date ?? null) from {{ $start_date }} @endif
                        @if ($end_date ?? null) to {{ $end_date }} @endif
                        — {{ ($summary['total'] ?? $bookings->count()) }} request(s),
                        ${{ number_format((float) ($summary['amount_usd'] ?? 0), 2) }} total
                    </div>
                @else
                    <div class="alert alert-info mt-2">
                        All reservations — {{ ($summary['total'] ?? $bookings->count()) }} request(s),
                        ${{ number_format((float) ($summary['amount_usd'] ?? 0), 2) }} total
                    </div>
                @endif
    
                <table class="table print-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Guest</th>
                            <th>Stay</th>
                            <th>Room</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Channel</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $rs)
                            <tr>
                                <td>{{ $rs->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    {{ $rs->guest_name }}<br>
                                    {{ $rs->guest_email }}<br>
                                    {{ $rs->guest_phone }}
                                </td>
                                <td>
                                    {{ $rs->check_in?->format('Y-m-d') }} → {{ $rs->check_out?->format('Y-m-d') }}
                                    ({{ $rs->nightsCount() }} night(s))
                                </td>
                                <td>{{ $rs->roomSummary() }}</td>
                                <td>{{ $rs->formattedTotalUsd() }}</td>
                                <td>{{ \App\Models\GuestBookingRequest::paymentLabel($rs->payment_method) }}</td>
                                <td>{{ \App\Models\GuestBookingRequest::channelLabel($rs->fulfillment_choice) }}</td>
                                <td>{{ \App\Models\GuestBookingRequest::statusLabel($rs->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-muted text-center">No reservations found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">Range total</th>
                            <th>${{ number_format((float) ($summary['amount_usd'] ?? 0), 2) }}</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        function printPage() {
            window.print();
        }
    </script>


@endsection