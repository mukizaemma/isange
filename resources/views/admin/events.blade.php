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
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Events</li>
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
                                    class="fa fa-plus"></i> Ajouter nouveau</button>

                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>Galerie</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($events as $rs)
                                        <tr>
                                            <td>{{ $rs->title_fr }}</td>
                                            <td><img src="{{ asset('storage/images/events/' . $rs->image) }}" alt="" width="150px"></td>
                                            <td>
                                                <a href="{{route('image.index', ['pid' =>$rs->id])}}" onclick="return !window.open(this.href, '', 'top=50 left=100 width=1100, height=700')">
                                                    <img src="assets/admin/assets/img/gallery.png" alt="" width="90px">
                                                    </a>
                                            </td>
                                            <td>{!! $rs->description_fr !!}</td>
                                            <td>{{$rs->date}}</td>
                                            <td>{{$rs->status}}</td>
                                            {{-- <td>{{$rs->status}}</td> --}}
                                            <td>
                                                <div class="btn-btn-group ">
                                                    <a type="button" href="{{ route('editEvent', $rs->id) }}" class="btn btn-primary text-black">Modifier</a>
                                                    <a type="button" href="{{ route('destroyEvent', $rs->id) }}"class="btn btn-danger text-black"
                                                        onclick="return confirm('Are you sure to delete this item?')">Supprimer</a>
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
                                    <h4 class="modal-title">Ajout de nouveau</h4>
                                    <button type="button" class="btn-close text-black" data-bs-dismiss="modal">X</button>
                                </div>

                                <!-- Modal body -->
                                <div class="modal-body">
                                    <form class="form" action="{{ route('saveEvent') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">

                                            <div class="row mb-3">
                                                <div class="col-lg-8 col-sm-12">
                                                    <label for="title" class="form-label">Titre de l'événement</label>
                                                    <input type="text" name="title_fr" class="form-control"
                                                        id="title" placeholder="Event Title" required="">
                                                </div>
                                                <div class="col-lg-4 col-sm-12">
                                                    <label for="title" class="form-label">A venir ou passé ?</label>
                                                    <select name="status" id="">
                                                        <option value=""></option>
                                                        <option value="Upcoming">Événement à venir</option>
                                                        <option value="Recent">Évènement passét</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="title" class="form-label">Date</label>
                                                    <input type="text" name="date" class="form-control"
                                                        id="title">
                                                </div>
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="author" class="form-label">Time</label>
                                                    <input type="text" name="time" class="form-control"
                                                        id="author">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="summernote" class="form-label">Description</label>
                                                    {{-- <textarea class="form-control" id="blogBody" rows="5" name="body"></textarea> --}}
                                                    <textarea id="eventDescription" rows="5" class="form-control" name="description_fr"></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="image" class="form-label">Photo de l'evenement<br> <span
                                                            style="color: red">(Cette image doit être redimensionnée à 500X800
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
                                                <i class="fa fa-save"></i> Ajouter
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
