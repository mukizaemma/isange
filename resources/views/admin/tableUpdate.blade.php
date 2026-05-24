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

                </ol>
                <div class="row">
                    @if(session()->has('success'))
                    <div class="arlert alert-success">
                        <button class="close" type="button" data-dismiss="alert">X</button>
                        {{ session()->get('success') }}
                    </div>
                    @endif

                    @if(session()->has('warning'))
                    <div class="arlert alert-warning">
                        <button class="close" type="button" data-dismiss="alert">X</button>
                        {{ session()->get('warning') }}
                    </div>
                    @endif
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <a href="{{route('tableCrud')}}" class="btn btn-primary">Back</a>
                    </div>
                    <div class="card-body">
                        <form class="form" action="{{ route('updateTable', $table->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="modal-body">
                                <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="name">Table Title</label>
                                    <input type="text" id="roomName" class="form-control" value="{{ $table->name }}" name="name" required="">
                                </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for="quantity">Quantity</label>
                                        <input type="text" id="quantity" class="form-control" value="{{ $table->qty }}" name="qty">

                                    </div>
                                    <div class="col-md-3">
                                        <label for="children">Max Seats</label>
                                        <input type="text" id="children" class="form-control" value="{{ $table->capacity }}" name="capacity" required="">

                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="summernote" class="form-label">Description</label>
                                        <textarea id="tableDescription" rows="5" class="form-control" name="description">{!! $table->description !!}</textarea>
                                    </div>
                                </div>

                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <label for="image" class="form-label">Cover Image<br></label>
                                        <img src="{{ asset('storage/images/tables/' . $table->image) }}" alt="" width="120px">
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="image" class="form-label">Change the Cover Image<br> <span
                                            style="color: red">(This Image should not exceed 500X800
                                            pixels)</span></label>
                                    <div class="input-group">

                                        <input type="file" name="image" class="form-control"
                                            id="image">

                                    </div>
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
                </div>


            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>

@section('scripts')



@endsection
