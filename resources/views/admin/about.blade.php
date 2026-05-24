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
                                <h2 class="btn btn-primary">Company Home Page Setting</h2>
                                @if (session()->has('success'))
                                    <div class="arlert alert-success">
                                        <button class="close" type="button" data-dismiss="alert">X</button>
                                        {{ session()->get('success') }}
                                    </div>
                                @endif

                            </div>
                            <!-- ./card-header -->
                            <div class="card-body">
                                <form class="form" action="{{ route('saveAbout', $data->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label for="projectinput8">Welcome Message</label>
                                                <textarea id="welcome" rows="5" class="form-control" name="welcome" placeholder="Welcome Message">{!!$data->welcome!!}</textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label for="projectinput8">Terms and conditions</label>
                                                <textarea id="terms" rows="5" class="form-control" name="terms" placeholder="Terms & Conditions">{!!$data->terms!!}</textarea>
                                            </div>
                                        </div>

                                        <div class="alert alert-info mt-4 mb-0">
                                            <i class="fas fa-panorama me-1"></i>
                                            Page hero images and captions are managed under
                                            <a href="{{ route('pageHeaders') }}" class="alert-link fw-semibold">Page banners</a>
                                            (Accommodation, Contact, About, and other inner pages).
                                        </div>

                                    </div>

                                    <div class="form-actions mt-5">
                                        <button type="submit" class="btn btn-primary text-black">
                                            <i class="fa fa-save"></i> Save Changes
                                        </button>

                                    </div>
                                </form>

                            </div>
                            <!-- /.card-body -->
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
