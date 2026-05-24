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
                        <li class="breadcrumb-item active">Home Sliding Images</li>
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
                                        <th>Title</th>
                                        <th>Table Image</th>
                                        <th>Max People</th>
                                        <th>Qty</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($tables as $rs)
                                        <tr>
                                            <td>{{ $rs->name }}</td>
                                            <td><img src="{{ asset('storage/images/tables/' . $rs->image) }}" alt="" width="150px"></td>
                                            {{-- <td>
                                                <a href="{{route('roomImage', ['pid' =>$rs->id])}}" onclick="return !window.open(this.href, '', 'top=50 left=100 width=1100, height=700')">
                                                    <img src="assets/admin/assets/img/gallery.png" alt="" width="90px">
                                                    </a>
                                                    <span>{{ count($rs->roomImages) }}</span>
                                            </td> --}}
                                            <td>{{ $rs->capacity }}</td>
                                            <td>{{ $rs->qty }}</td>
                                            <td>{!! $rs->description !!}</td>
                                            {{-- <td>{{$rs->status}}</td> --}}
                                            <td>
                                                <div class="btn-btn-group ">
                                                    <a type="button" href="{{ route('editTable', $rs->id) }}" class="btn btn-primary text-black">Edit</a>
                                                    <a type="button" href="{{ route('destroyTable', $rs->id) }}"class="btn btn-danger text-black"
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
                                    <h4 class="modal-title">Adding New Table</h4>
                                    <button type="button" class="btn-close text-black" data-bs-dismiss="modal">X</button>
                                </div>

                                <!-- Modal body -->
                                <div class="modal-body">
                                    <form class="form" action="{{ route('saveTable') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                               <div class="row mt-3">
                                                 <div class="col-md-6">
                                                    <label for="name">Table Title</label>
                                                    <input type="text" id="roomName" class="form-control" placeholder="Table number/Name" name="name" required="">
                                                 </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-3">
                                                        <label for="quantity">Quantity</label>
                                                        <input type="text" id="quantity" class="form-control" placeholder="How many of this kind?" name="qty">

                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="children">Max Seats</label>
                                                        <input type="text" id="children" class="form-control" placeholder="Maximum Adults Ocupation" name="capacity" required="">

                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label for="summernote" class="form-label">Description</label>
                                                        <textarea id="tableDescription" rows="5" class="form-control" name="description"></textarea>
                                                    </div>
                                                </div>

                                            <div class="row">
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="image" class="form-label">Cover Image<br> <span
                                                            style="color: red">(This Image should not exceed 500X800
                                                            pixels)</span></label>
                                                    <div class="input-group">

                                                        <input type="file" name="image" class="form-control"
                                                            id="image">

                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary text-black">
                                                <i class="fa fa-save"></i> Add Table
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
