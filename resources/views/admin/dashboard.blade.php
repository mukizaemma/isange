@extends('layouts.adminbase')

@section('title', 'Home Page')

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
                <h1 class="mt-4"></h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Inscriptions en ligne
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="250px">Date</th>
                                    <th width="250px">Customer Names</th>
                                    <th width="250px">Customer Phone</th>
                                    <th width="200px">Room</th>
                                    <th width="250px">CheckIn Date</th>
                                    <th width="250px">CheckOut Date</th>
                                    <th>Adults/Children</th>
                                    <th>Additional Request</th>
                                    <th width="200px">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($bookings as $rs)
                                    <tr>
                                        <td>{{ $rs->created_at }}</td>
                                        <td>{{ $rs->names }}</td>
                                        <td>{{ $rs->phone }}</td>
                                        <td>{{ $rs->room->roomName ?? '' }}</td>
                                        <td>{{ $rs->checkin }}</td>
                                        <td>{{ $rs->checkout }}</td>
                                        <td>{{ $rs->adults }} / {{ $rs->children }}</td>
                                        <td>{{ $rs->description }}</td>
                                        <td>
                                            <div class="btn-btn-group">
                                                <a type="button" href="{{ route('viewBooking', $rs->id) }}" class="btn btn-primary text-black"><i class="fa fa-eye"></i> </a>
                                                <a type="button" href="{{ route('roomTypeDelete', $rs->id) }}"class="btn btn-danger text-black"
                                                    onclick="return confirm('Are you sure to delete this item?')"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Your Website 2021</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

@endsection
