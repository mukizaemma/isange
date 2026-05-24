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

                                        <div class="row mt-4">
                                            @php
                                                $imageSections = [
                                                    ['label' => 'Why Choose Us Image', 'field' => 'aboutImage'],
                                                    ['label' => 'Contact Us Header Image', 'field' => 'middleImage'],
                                                    ['label' => 'Rooms Page Header Image', 'field' => 'chooseusImage'],
                                                ];
                                            @endphp
                                        
                                            @foreach ($imageSections as $section)
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="card shadow-lg border-0">
                                                        <div class="card-body text-center">
                                                            <h6 class="mb-3">{{ $section['label'] }}</h6>
                                        
                                                            <!-- Image Preview -->
                                                            <div class="image-preview mb-3">
                                                                <img src="{{ asset('storage/images/gallery/'.$data->{$section['field']}) }}" 
                                                                    alt="Current Image" class="img-fluid rounded shadow" width="150px">
                                                            </div>
                                        
                                                            <!-- Upload New Image -->
                                                            <label class="custom-file-upload btn btn-outline-success btn-sm">
                                                                <input type="file" name="{{ $section['field'] }}" class="d-none" onchange="previewImage(event, '{{ $section['field'] }}')">
                                                                <i class="fas fa-upload"></i> Change Image
                                                            </label>
                                        
                                                            <p class="text-muted mt-2" style="font-size: 12px;">
                                                                Recommended Size: <b>540x600 pixels</b>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
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
