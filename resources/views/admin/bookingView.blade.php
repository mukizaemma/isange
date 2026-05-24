@extends('layouts.adminbase')

@section('title', 'Speakers')

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
                    {{-- <h1 class="mt-4">Dashboard</h1> --}}
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Facilities</li>
                    </ol>
                    <div class="row">
                        @if (session()->has('success'))
                            <div class="arlert alert-success">
                                <button class="close" type="button" data-dismiss="alert">X</button>
                                {{ session()->get('success') }}
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="arlert alert-danger">
                                <button class="close" type="button" data-dismiss="alert">X</button>
                                {{ session()->get('error') }}
                            </div>
                        @endif
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <a href="{{ route('bookings') }}" class="btn btn-primary">Back to Bookings</a>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-dm-6 colsm-12">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="250px">Date Done</th>
                                            </tr>
                                        </thead>
        
                                        <tbody>
                                                <tr>
                                                    <td>{{ $booking->created_at }}</td>
                                                </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-dm-6 colsm-12"></div>
                            </div>
                        </div>
                    </div>


                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>

@section('scripts')



@endsection
