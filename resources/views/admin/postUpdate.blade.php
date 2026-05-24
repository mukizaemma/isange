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
                <a href="{{ route('postCrud') }}" class="btn btn-primary">Retour</a>
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
                            <form class="form" action="{{ url('updatePost',$post->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-lg-8 col-sm-12">
                                            <label for="title" class="form-label">Titre de l'actualite</label>
                                            <input type="text" name="title_fr" class="form-control"
                                                id="title" value="{{$post->title_fr}}">
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="summernote" class="form-label">Description</label>
                                            {{-- <textarea class="form-control" id="blogBody" rows="5" name="body"></textarea> --}}
                                            <textarea id="postDescription" rows="5" class="form-control" name="description_fr">{!!$post->description_fr!!}</textarea>
                                        </div>
                                    </div>

                                    <div  class="row mt-3">
                                        <div class="col-lg-6 col-sm-12">
                                            <label for="image" class="form-label">Photo de l'actualite<br> <span style="color: red">(Cette image doit être redimensionnée à 500X800
                                                pixels)</span></label>
                                            <img src="{{ asset('storage/images/posts/' . $post->image) }}" alt="" width="120px">
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <label for="image" class="form-label">Modifier la photo<br> <span style="color: red">(This image should be resized to 500X800 pixels)</span></label>
                                            <div class="input-group">

                                                <input type="file" name="image" class="form-control" id="image">

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary text-black">
                                        <i class="fa fa-save"></i> Sauvegarder les modifications
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
