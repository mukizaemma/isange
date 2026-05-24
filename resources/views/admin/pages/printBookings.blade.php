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
                @if (isset($start_date) && isset($end_date))
                    <div class="alert alert-info mt-2">
                        Reservations from: {{ $start_date }} to {{ $end_date }}
                    </div>
                @endif
    
                <table class="table print-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Names</th>
                            <th>Phone</th>
                            <th>Room</th>
                            <th>CheckIn</th>
                            <th>CheckOut</th>
                            <th>Adults</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $rs)
                            <tr>
                                <td>{{ $rs->created_at }}</td>
                                <td>{{ $rs->names }}</td>
                                <td>{{ $rs->phone }}</td>
                                <td>{{ $rs->room }}</td>
                                <td>{{ $rs->checkin }}</td>
                                <td>{{ $rs->checkout }}</td>
                                <td>{{ $rs->adults }}</td>
                            </tr>
                        @endforeach
                    </tbody>
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