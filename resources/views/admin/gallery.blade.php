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
                    <li class="breadcrumb-item active">Images </li>
                </ol>
                <div class="row">
                    @if(session()->has('success'))
                    <div class="arlert alert-success">
                        <button class="close" type="button" data-dismiss="alert">X</button>
                        {{ session()->get('success') }}
                    </div>

                    @endif
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <button class="btn btn-primary float-right" data-bs-toggle="modal"
                            data-bs-target="#myModal"><i class="fa fa-plus"></i> Add New Image</button>

                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Display Page </th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($images as $rs)
                                <tr>
                                   
                                    <td><img src="{{asset('storage/images/gallery').$rs->image}}" alt="" width="150px"></td>
                                    <td>{{$rs->category ?? ''}}</td>
                                    <td>                                                
                                        <div class="btn-btn-group ">
                                        <a type="button" href="{{ route('editGallery', $rs->id) }}"
                                            class="btn btn-primary text-black">Edit</a>
                                        <a type="button" href="{{ route('destroyGallery', $rs->id) }}"
                                            class="btn btn-danger text-black" onclick="return confirm('Are you sure to delete this item?')">Delete</a>
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
                                        <h4 class="modal-title">Add New to Gallery Page</h4>
                                        <button type="button" class="btn-close text-black"
                                            data-bs-dismiss="modal"></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <form class="form" action="{{ route('saveGallery') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <div class="row mb-4">

                                                    <div class="col-lg-8 col-sm-12">
                                                            <label>Photo <br><span style="color: red"></label>
                                                            <label id="projectinput7" class="file center-block">
                                                                <input type="file" id="image" name="image"
                                                                    required="">
                                                                <span class="file-custom"></span>
                                                            </label>
                                                    </div>                                                

                                                </div>

                                                <div class="col-lg-4 col-sm-12">
                                                    <label>Image Category</label>
                                                    <select class="form-control border-success" name="category" id="category" required onchange="toggleCaptionField()">
                                                        <option value="" disabled selected>Select Category</option>
                                                        <option value="Accommodation">Accommodation</option>
                                                        <option value="Outdoor">Outdoor</option>
                                                        <option value="Facilities">Facilities</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 col-sm-12" id="caption-field" style="display: none;">
                                                    <label for="projectinput8">Image Caption</label>
                                                    <input type="text" class="form-control" placeholder="Image Caption" name="caption">
                                                </div>                                                

                                            </div>

                                            <div class="form-actions mt-5">
                                                <button type="submit" class="btn btn-primary text-black">
                                                    <i class="fa fa-save"></i> Add Image
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
