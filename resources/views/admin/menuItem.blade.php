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
                        <li class="breadcrumb-item active">Restaurant Menu Management</li>
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

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#menuItems">Menu Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#menuCategories">Menu Categories</a>
                        </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                        <div class="tab-pane container active" id="menuItems">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <button class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#menuItem"><i
                                            class="fa fa-plus"></i> Add New Menu Item</button>
        
                                </div>
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                {{-- <th>Category</th> --}}
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Duration</th>
                                                <th>Image</th>
                                                <th>Ingredients</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
        
                                        <tbody>
                                            @foreach ($menuItems as $rs)
                                                <tr>
                                                    {{-- <td>{{ $rs->menuCategory->name }}</td> --}}
                                                    <td>{{ $rs->name }}</td>
                                                    <td>{{ $rs->price }}</td>
                                                    <td>{{ $rs->duration }}</td>
                                                    <td><img src="{{ asset('storage/images/menu/' . $rs->image) }}" alt="" width="150px"></td>
                                                    <!-- <td>
                                                        <a href="{{route('image.index', ['pid' =>$rs->id])}}" onclick="return !window.open(this.href, '', 'top=50 left=100 width=1100, height=700')">
                                                            <img src="assets/admin/assets/img/gallery.png" alt="" width="90px">
                                                            </a>
                                                    </td> -->
                                                    <td>{!! $rs->description !!}</td>
                                                    {{-- <td>{{$rs->status}}</td> --}}
                                                    <td>
                                                        <div class="btn-btn-group ">
                                                            <a type="button" href="{{ route('editFacil', $rs->id) }}" class="btn btn-primary text-black">Edit</a>
                                                            <a type="button" href="{{ route('destroyMenu', $rs->id) }}"class="btn btn-danger text-black"
                                                                onclick="return confirm('Are you sure to delete this item?')">Delete</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        {{-- Categories --}}
                        <div class="tab-pane container fade" id="menuCategories">

                            <div class="card mb-4">
                                <div class="card-header">
                                    <button class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#menuCategory"><i
                                            class="fa fa-plus"></i> Add New Category</button>
        
                                </div>
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
        
                                        <tbody>
                                            @foreach ($categories as $rs)
                                                <tr>
                                                    <td>{{ $rs->name }}</td>
                                                    <td>{!! $rs->description !!}</td>
                                                    {{-- <td>{{$rs->status}}</td> --}}
                                                    <td>
                                                        <div class="btn-btn-group ">
                                                            <a type="button" href="{{ route('destroyFacil', $rs->id) }}"class="btn btn-danger text-black"
                                                                onclick="return confirm('Are you sure to delete this item?')">Delete</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        </div>


                        <!-- The Modal for adding new Event -->
                    <div class="modal fade" id="menuItem">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title">Adding New Menu Item</h4>
                                    <button type="button" class="btn-close text-black" data-bs-dismiss="modal">X</button>
                                </div>

                                <!-- Modal body -->
                                <div class="modal-body">
                                    <form class="form" action="{{ route('saveMenu') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">

                                            <div class="row mb-3">
                                                <div class="col-lg-5 col-sm-12">
                                                    <label for="title" class="form-label">Menu Category</label>
                                                    <select name="menucategory_id" id="">
                                                        <option >-- Select Category --</option>
                                                        @foreach($categories as $rs)
                                                        <option value="{{ $rs->id }}">{{ $rs->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-lg-5 col-sm-12">
                                                    <label for="title" class="form-label">Menu Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        id="title" placeholder="Menu Name" required="">
                                                </div>
                                            
                                            
                                                <div class="col-lg-3 col-sm-12">
                                                    <label for="title" class="form-label">Price</label>
                                                    <input type="text" name="price" class="form-control"
                                                        id="title" placeholder="Menu Name" required="">
                                                </div>
                                        
                                        
                                                <div class="col-lg-4 col-sm-12">
                                                    <label for="title" class="form-label">Duration</label>
                                                    <input type="text" name="duration" class="form-control"
                                                        id="title" placeholder="Eg. 15 Minutes" required="">
                                                </div>

                                                <div class="col-lg-4 col-sm-12">
                                                    <label for="title" class="form-label">Duration</label>
                                                    <select name="preplocation" id="">
                                                        <option >-- Preparetion Location --</option>
                                                        <option value="Kitchen">Kitchen</option>
                                                        <option value="Bar">Bar</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="summernote" class="form-label">Menu Ingredients</label>
                                                    {{-- <textarea class="form-control" id="blogBody" rows="5" name="body"></textarea> --}}
                                                    <textarea id="activity" rows="5" class="form-control" name="description"></textarea>
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
                                                <i class="fa fa-save"></i> Add Menu
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

                        <!-- The Modal for adding new Category -->
                    <div class="modal fade" id="menuCategory">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title">Adding New Category</h4>
                                    <button type="button" class="btn-close text-black" data-bs-dismiss="modal">X</button>
                                </div>

                                <!-- Modal body -->
                                <div class="modal-body">
                                    <form class="form" action="{{ route('saveMenuCateg') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">

                                            <div class="row mb-3">
                                                <div class="col-lg-12">
                                                    <label for="title" class="form-label">Category Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        id="title" placeholder="Menu Category" required="">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="summernote" class="form-label">Category Description</label>
                                                    {{-- <textarea class="form-control" id="blogBody" rows="5" name="body"></textarea> --}}
                                                    <textarea id="activity" rows="5" class="form-control" name="description"></textarea>
                                                </div>
                                            </div>

                                            {{-- <div class="row">
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="image" class="form-label">Cover Image<br> <span
                                                            style="color: red">(This Image should not exceed 500X800
                                                            pixels)</span></label>
                                                    <div class="input-group">

                                                        <input type="file" name="image" class="form-control"
                                                            id="image">

                                                    </div>
                                                </div>

                                            </div> --}}
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary text-black">
                                                <i class="fa fa-save"></i> Add New Category
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
