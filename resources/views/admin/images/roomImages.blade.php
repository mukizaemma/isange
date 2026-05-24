@extends('layouts.adminWindow')

@section('title', 'Rooms')

@section('content')

<div id="layoutSidenav">

    <div id="layoutSidenav_content">
        <main>
                    <!-- Main content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h1 class="mb-0">Images for <strong>{{ $room->roomName }}</strong></h1>
                            <button type="button" class="btn btn-secondary" onclick="closeGalleryWindow()">
                                <i class="fa fa-times"></i> Close
                            </button>
                        </div>
                        @include('admin.includes.validation-alert')

                        <div class="card">
                            <div class="card-header">
                                <form class="form" id="roomGalleryForm" action="{{ route('savRoomImage',['pid' =>$room->id]) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="image">Gallery image <span class="text-danger">*</span></label>
                                                    <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                                                        required accept="image/jpeg,image/png,image/gif,image/webp">
                                                    @error('image')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                        </div>

                                    </div>

                                    <div class="form-actions mt-3 text-black">
                                        <button type="submit" class="btn btn-primary text-black">
                                            <i class="fa fa-save"></i> Add Image
                                        </button>

                                    </div>
                                </form>

                            </div>
                            <!-- ./card-header -->
                            <div class="card-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>PID</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($images as $imag)
                                        <td><img src="{{ asset('storage/images/rooms') . $imag->image }}"
                                            alt="" width="120"></td>
                                            <td>{{ $imag->room_id }}</td>
                                            <td>
                                                <div class="btn-btn-group">
                                                    {{-- <a type="button" href="{{ route('editImage', ['pid' =>$tours->id, 'id' =>$imag->id]) }}"
                                                        class="btn btn-primary">Edit</a> --}}
                                                    <a type="button" href="{{ route('destroyRoomImage', ['pid' =>$imag->id, 'id' =>$imag->id]) }}"
                                                        class="btn btn-danger" onclick="return confirm('Deleting !! Are you sure ?')">Delete</a>
                                                </div>
                                            </td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer text-end">
                                <button type="button" class="btn btn-secondary" onclick="closeGalleryWindow()">
                                    <i class="fa fa-times"></i> Close
                                </button>
                            </div>
                        </div>
                        <!-- /.card -->


                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
        </main>


    </div>
</div>

@endsection

@section('scripts')
<script>
    var galleryForm = document.getElementById('roomGalleryForm');
    if (galleryForm) {
        galleryForm.addEventListener('submit', function (e) {
            var imageEl = document.getElementById('image');
            if (!imageEl || !imageEl.files || !imageEl.files.length) {
                e.preventDefault();
                imageEl.classList.add('is-invalid');
                imageEl.focus();
            }
        });
    }

    function closeGalleryWindow() {
        if (window.opener) {
            window.close();
            return;
        }

        window.location.href = @json(route('getRooms'));
    }
</script>
@endsection
