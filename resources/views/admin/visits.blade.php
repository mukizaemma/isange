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
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Daily</td>
                                    <td>{{ $dailyVisits }}</td>
                                </tr>
                                <tr>
                                    <td>Weekly</td>
                                    <td>{{ $weeklyVisits }}</td>
                                </tr>
                                <tr>
                                    <td>Annual</td>
                                    <td>{{ $annualVisits }}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>{{ $totalVisits }}</td>
                                </tr>
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
