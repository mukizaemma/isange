@extends('layouts.adminbase')

@section('title', 'Modifier')

@section('sidebar')

    @parent

@endsection

@section('content')

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            @include('admin.includes.sidenav')
        </div>
        <div id="layoutSidenav_content">
            <div class="card-header">
                <a href="{{ route('getServices') }}" class="btn btn-primary">Back</a>
                @if (session()->has('success'))
                    <div class="arlert alert-success">
                        <button class="close" type="button" data-dismiss="alert">X</button>
                        {{ session()->get('success') }}
                    </div>
                @endif
            </div>
            <main>
                <div class="container-fluid px-4">
                    <div class="row">

                    </div>

                    <div class="card mb-4">

                        <div class="card-body">
                            <form class="form" action="{{ url('updateService',$service->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                        <div class="modal-body">

                                            <div class="row mb-3">
                                                <div class="col-lg-8 col-sm-12">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="{{$service->title}}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="summernote" class="form-label">Description</label>
                                                    {{-- <textarea class="form-control" id="blogBody" rows="5" name="body"></textarea> --}}
                                                    <textarea id="postDescription" rows="5" class="form-control" name="description">{{$service->description}}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="image" class="form-label">Featured Cover Image<br></label>
                                                        <img src="{{ asset('storage/images/services/' . $service->image) }}" alt="" width="120px">
                                                </div>
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="image" class="form-label">Change the Cover Image</label>
                                                    <div class="input-group">

                                                        <input type="file" name="image" class="form-control"
                                                            id="image">

                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary text-black">
                                        <i class="fa fa-save"></i> Save Changes
                                    </button>

                                </div>
                            </form>
                        </div>
                    </div>


                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>

@section('scripts')

    <script src="{{ asset('assets') }}/js/summernote.js"></script>
@endsection
