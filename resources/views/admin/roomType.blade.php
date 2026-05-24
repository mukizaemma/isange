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
                        <li class="breadcrumb-item active">Room types</li>
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
                            <button class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#myModal"><i
                                    class="fa fa-plus"></i> Add New</button>

                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Room Type Name</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($rooms as $rs)
                                        <tr>
                                            <td>{{ $rs->name }}</td>
                                            <td>{!! $rs->description !!}</td>
                                            {{-- <td>{{$rs->status}}</td> --}}
                                            <td>
                                                <div class="btn-btn-group ">
                                                    <a type="button" href="#" class="btn btn-primary text-black">Edit</a>
                                                    <a type="button" href="{{ route('roomTypeDelete', $rs->id) }}"class="btn btn-danger text-black"
                                                        onclick="return confirm('Are you sure to delete this item?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- The Modal for adding new Event -->
                    <div class="modal fade" id="myModal">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title">Adding New Room Type</h4>
                                    <button type="button" class="btn-close text-black" data-bs-dismiss="modal">X</button>
                                </div>

                                <!-- Modal body -->
                                <div class="modal-body">
                                    <form class="form" action="{{ route('roomTypeCreate') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="name">Room Name</label>
                                                <input type="text" id="roomName" class="form-control" placeholder="Type Room Name" name="name" required="">
                                            </div>

                                        </div>         
                                        <div class="row">
                                            <div class="col-lg-12">
                                                 <label for="summernote" class="form-label">Description</label>
                                                    <textarea id="roomDescription" rows="5" class="form-control" name="description"></textarea>
                                            </div>
                                        </div>
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary text-black">
                                                <i class="fa fa-save"></i> Add New
                                            </button>

                                        </div>
                                    </form>
                                </div>

                                <!-- Modal footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger text-black"
                                        data-bs-dismiss="modal">Close</button>
                                </div>

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
