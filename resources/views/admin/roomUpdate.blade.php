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
                        <a href="{{route('getRooms')}}" class="btn btn-primary">Back</a>
                    </div>
                    <div class="card-body">
                    <form class="form" action="{{ route('updateRoom', $room->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="roomName">Room Name</label>
                                    <input type="text" id="roomName" class="form-control" value="{{ $room->roomName }}" name="roomName">
                                </div>
                                <div class="col-md-3">
                                    <label for="price">Room Price in USD</label>
                                    <input type="text" id="price" class="form-control" value="{{ $room->price }}" name="price">
                                </div>
                                <div class="col-md-3">
                                    <label for="price_rwf">Price (RWF, optional)</label>
                                    <input type="number" step="1" min="0" id="price_rwf" class="form-control" value="{{ $room->price_rwf ?? '' }}" name="price_rwf" placeholder="Exact local amount">
                                </div>
                            </div>
                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <label for="accommodation_type">Listing type</label>
                                            <select name="accommodation_type" id="accommodation_type" class="form-select" required>
                                                @foreach ($accommodationTypes as $type)
                                                    <option value="{{ $type }}" @selected(($room->accommodation_type ?? 'room') === $type)>{{ ucfirst($type) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="category">Room style</label>
                                            <select name="category" id="category" class="form-select">
                                                <option value="">—</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat }}" @selected($room->category === $cat)>{{ ucfirst($cat) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="quantity">Room Size</label>
                                            <input type="text" id="quantity" class="form-control" value="{{ $room->size }}" name="size">

                                        </div>
                                        <div class="col-md-3">
                                            <label for="children">Max Adults</label>
                                            <input type="text" id="children" class="form-control" value="{{ $room->maxAdults }}" name="maxAdults" required="">

                                        </div>
                                            <div class="col-md-3">
                                            <label for="children">Max Children</label>
                                            <input type="text" id="children" class="form-control" value="{{ $room->maxChildren }}" name="maxChildren" required="">

                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label for="summernote" class="form-label">Description</label>
                                            <textarea id="roomDescription" rows="5" class="form-control" name="description">{!! $room->description !!}</textarea>
                                        </div>
                                    </div>

                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="image" class="form-label">Cover Image</label>
                                        <div class="input-group">
                                            <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="" width="120px">

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="image" class="form-label">Cover Image</label>
                                        <div class="input-group">

                                            <input type="file" name="image" class="form-control"
                                                id="image">

                                        </div>
                                    </div>

                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Room amenities</label>
                                        <p class="small text-muted">Tick everything this room includes.</p>
                                        <div class="row row-cols-1 row-cols-md-2 g-2">
                                            @foreach ($amenityOptions as $opt)
                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="amenity_options[]" value="{{ $opt->id }}" id="amenity-opt-{{ $opt->id }}"
                                                            {{ $room->amenityOptions->contains($opt->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="amenity-opt-{{ $opt->id }}">{{ $opt->label }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
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
<script>
$(document).ready(function () {
    $('#roomDescription').summernote({
        placeholder: 'Room description…',
        tabsize: 2,
        height: 280,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});
</script>
@endsection
