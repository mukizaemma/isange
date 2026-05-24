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
                                <a href="{{ route('pagesIndex') }}" class="btn btn-primary">Back to Pages</a>
                                @if (session()->has('success'))
                                    <div class="arlert alert-success">
                                        <button class="close" type="button" data-dismiss="alert">X</button>
                                        {{ session()->get('success') }}
                                    </div>
                                @endif

                            </div>
                            <!-- ./card-header -->
                            <div class="card-body">
                                <h1>Reservations Policy</h1>
                                <form class="form" action="{{ route('saveReserve', $data->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="form-group">
                                                    <label for="projectinput1">Title</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $data->title }}" name="title">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label for="summernote" class="form-label">Sammary</label>
                                                {{-- <textarea class="form-control" id="blogBody" rows="5" name="body"></textarea> --}}
                                                <textarea id="reserveDescription" rows="5" class="form-control" name="description">{!! $data->description !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label for="summernote" class="form-label">Detailed Description</label>
                                                <textarea id="reserveDetails" rows="5" class="form-control" name="details">{!! $data->details !!}</textarea>
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="col-lg-6 col-sm-12">
                                                <label>Page's Cover Image </label><br>
                                                <label id="projectinput7" class="file center-block">
                                                    <img src="{{ asset('storage/images') . $data->cover }}"
                                                        alt="" width="150px">
                                                </label>
                                            </div>

                                            <div class="col-lg-6 col-sm-12">
                                                <label>Change the Cover Image <br><span style="color: red">(This
                                                        image should be resized to 500X800 pixels)</span></label>
                                                <label id="projectinput7" class="file center-block">
                                                    <input type="file" id="image" name="cover">
                                                    <span class="file-custom"></span>
                                                </label>
                                            </div>
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
