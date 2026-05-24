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
                    <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                {{-- <a href="{{ route('pagesIndex') }}" class="btn btn-primary">Restaurant Settings</a> --}}
                                @if (session()->has('success'))
                                    <div class="arlert alert-success">
                                        <button class="close" type="button" data-dismiss="alert">X</button>
                                        {{ session()->get('success') }}
                                    </div>
                                @endif

                                <div class="btn-group">
                                    <a href="{{ route('restaurant') }}" class="btn btn-primary">Restaurant Settings</a>
                                    <a href="{{ route('clubCR') }}" class="btn btn-primary">Club</a>
                                    <a href="{{ route('reservation') }}" class="btn btn-primary">Reservation Policy</a>
                                  </div>

                            </div>
                        </div>
                        <!-- /.card -->


                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
        </main>
        @include('admin.includes.footer')
    </div>
</div>

@endsection
