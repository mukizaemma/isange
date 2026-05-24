@extends('layouts.frontbase')

@section('title', 'Tours')

@section('content')
<head>
    <title>Print Booking List</title>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-table, .print-table * {
                visibility: visible;
            }
            .print-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
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
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Booking List</h2>
                <button onclick="printPage()" class="btn btn-primary">Print</button>
            </div>
            <div class="card-body">
                @if (isset($start_date) && isset($end_date))
                    <div class="alert alert-info mt-2">
                        Selected Date Range: {{ $start_date }} to {{ $end_date }}
                    </div>
                @endif
    
                <table class="table print-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer Names</th>
                            <th>Customer Phone</th>
                            <th>Room</th>
                            <th>Check-In Date</th>
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
                                <td>{{ $rs->adults }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
    
                <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Email</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>John</td>
                        <td>Doe</td>
                        <td>john@example.com</td>
                      </tr>
                      <tr>
                        <td>Mary</td>
                        <td>Moe</td>
                        <td>mary@example.com</td>
                      </tr>
                      <tr>
                        <td>July</td>
                        <td>Dooley</td>
                        <td>july@example.com</td>
                      </tr>
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


@endsection