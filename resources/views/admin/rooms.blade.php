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
                        <li class="breadcrumb-item active">Rooms</li>
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
                        @include('admin.includes.validation-alert')
                    </div>

                    <div class="card mb-4 border-success">
                        <div class="card-header bg-light">
                            <strong><i class="fas fa-tags me-1"></i> Discount all rooms</strong>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Apply one discount to every room with a USD price. You can still edit any room afterward to change or remove its discount.
                            </p>
                            <form method="POST" action="{{ route('rooms.bulkDiscount') }}" class="row g-3 align-items-end"
                                onsubmit="return confirm(event.submitter && event.submitter.value === 'remove' ? 'Remove discounts from all rooms?' : 'Apply this discount to all priced rooms?');">
                                @csrf
                                <div class="col-md-4">
                                    <label for="bulk_discount_type" class="form-label">Discount type</label>
                                    <select name="bulk_discount_type" id="bulk_discount_type" class="form-select @error('bulk_discount_type') is-invalid @enderror">
                                        <option value="percent" @selected(old('bulk_discount_type', 'percent') === 'percent')>Percentage (%)</option>
                                        <option value="fixed" @selected(old('bulk_discount_type') === 'fixed')>Fixed amount (USD)</option>
                                    </select>
                                    @error('bulk_discount_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="bulk_discount_value" class="form-label">Discount value</label>
                                    <input type="number" step="0.01" min="0.01" name="bulk_discount_value" id="bulk_discount_value"
                                        class="form-control @error('bulk_discount_value') is-invalid @enderror"
                                        value="{{ old('bulk_discount_value') }}" placeholder="e.g. 20">
                                    @error('bulk_discount_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5 d-flex flex-wrap gap-2">
                                    <button type="submit" name="action" value="apply" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i> Apply to all priced rooms
                                    </button>
                                    <button type="submit" name="action" value="remove" class="btn btn-outline-danger" formnovalidate>
                                        Remove all discounts
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <button class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#RoomModal"><i
                                    class="fa fa-plus"></i> Add New Room</button>

                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Room Name</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Type</th>
                                        <th>Image</th>
                                        <th>Gallery</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($rooms as $rs)
                                        <tr>
                                            <td>{{ $rs->roomName }}</td>
                                            <td>
                                                @if ($rs->hasActiveDiscount())
                                                    <span class="text-decoration-line-through text-muted small">{{ \App\Support\Currency::formatRoomPriceLabel($rs->listPriceUsd()) }}</span><br>
                                                    {!! \App\Support\Currency::formatUsdWithLocal($rs->salePriceUsd(), $rs->salePriceRwf()) !!}
                                                    <br><span class="badge bg-success">{{ $rs->discountBadgeLabel() }}</span>
                                                @else
                                                    {!! \App\Support\Currency::formatUsdWithLocal($rs->price, $rs->price_rwf) !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($rs->hasActiveDiscount())
                                                    <span class="badge bg-success">On</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($rs->accommodation_type ?? 'room') }}</td>
                                            <td><img src="{{ asset('storage/images/rooms/' . $rs->image) }}" alt="" width="150px"></td>
                                            <td>
                                                <a href="{{route('roomImages', ['pid' =>$rs->id])}}" onclick="return !window.open(this.href, '', 'top=50 left=100 width=1100, height=700')">
                                                    <img src="assets/admin/assets/img/gallery.png" alt="" width="90px">
                                                    </a>
                                            </td>
                                            <td>{{ \Illuminate\Support\Str::limit(strip_tags($rs->description), 90) }}</td>
                                            <td>
                                                <div class="btn-btn-group ">
                                                    <a type="button" href="{{ route('editRoom', $rs->id) }}" class="btn btn-primary text-black">Edit</a>
                                                    <a type="button" href="{{ route('destroyRoom', $rs->id) }}"class="btn btn-danger text-black"
                                                        onclick="return confirm('Are you sure to delete this room?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>




                        <!-- The Modal for adding new room -->
                        <div class="modal fade" id="RoomModal">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Adding New Room</h4>
                                        <button type="button" class="btn-close text-black" data-bs-dismiss="modal">X</button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <form class="form" id="roomCreateForm" action="{{ route('saveRoom') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <label for="roomName">Room Name <span class="text-danger">*</span></label>
                                                    <input type="text" id="roomName" class="form-control @error('roomName') is-invalid @enderror" placeholder="Type Room Name" name="roomName" value="{{ old('roomName') }}" required maxlength="255">
                                                    @error('roomName')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="singlePrice">Price (USD / night) <span class="text-muted fw-normal">(optional)</span></label>
                                                    <input type="text" id="price" class="form-control" placeholder="Price/ Night" name="price">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="price_rwf">Price (RWF, optional)</label>
                                                    <input type="number" step="1" min="0" id="price_rwf" class="form-control" placeholder="Exact local amount" name="price_rwf">
                                                </div>
                                            </div>

                                            @include('admin.includes.room-discount-fields', ['discountFieldId' => 'create'])
                                    
                                            <div class="row mt-3">
                                                <div class="col-md-3">
                                                    <label for="accommodation_type">Listing type</label>
                                                    <select name="accommodation_type" id="accommodation_type" class="form-select @error('accommodation_type') is-invalid @enderror" required>
                                                        @foreach ($accommodationTypes as $type)
                                                            <option value="{{ $type }}" @selected(old('accommodation_type', 'room') === $type)>{{ ucfirst($type) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('accommodation_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="quantity">Room Size</label>
                                                    <input type="text" id="quantity" class="form-control" placeholder="How big is the room?" name="size">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="maxAdults">Max Adults</label>
                                                    <input type="text" id="maxAdults" class="form-control" placeholder="Maximum Adults Occupation" name="maxAdults">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="maxChildren">Max Children</label>
                                                    <input type="text" id="maxChildren" class="form-control" placeholder="Maximum Children Occupation" name="maxChildren">
                                                </div>
                                            </div>
                                    
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="roomDescription" class="form-label">Description</label>
                                                    <textarea id="roomDescription" rows="5" class="form-control" name="description"></textarea>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <label class="form-label">Room amenities</label>
                                                    <div class="row row-cols-1 row-cols-md-2 g-1" style="max-height:220px;overflow-y:auto;">
                                                        @foreach ($amenityOptions as $opt)
                                                            <div class="col">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="amenity_options[]" value="{{ $opt->id }}" id="modal-amenity-{{ $opt->id }}">
                                                                    <label class="form-check-label small" for="modal-amenity-{{ $opt->id }}">{{ $opt->label }}</label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                    
                                            <div class="row">
                                                <div class="col-lg-6 col-sm-12">
                                                    <label for="image" class="form-label">Cover Image <span class="text-danger">*</span><br> <span class="text-muted small">JPEG, PNG, or WebP — auto-compressed in your browser to under 700 KB before upload</span></label>
                                                    <div class="input-group">
                                                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" id="image" required accept="image/jpeg,image/png,image/gif,image/webp">
                                                        @error('image')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                    
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary text-black">
                                                    <i class="fa fa-save"></i> Add Room
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


                            <!-- The Modal for adding new Amenity -->
                        <div class="modal fade" id="amenity">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Adding New Amenity</h4>
                                        <button type="button" class="btn-close text-black" data-bs-dismiss="modal">X</button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <form class="form" action="{{ route('amenityCreate') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <label for="name">Amenity Name</label>
                                                    <input type="text" id="roomName" class="form-control" placeholder="Type Room Name" name="name" required="">
                                                </div>

                                            </div>         
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <label for="image" class="form-label">Amenity Icon<br> <span
                                                            style="color: red"></span></label>
                                                            <input type="text" id="roomName" class="form-control" placeholder="Icon Code" name="price" required="">
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
<script>
$(document).ready(function () {
    @if ($errors->hasAny(['image', 'roomName', 'accommodation_type', 'price', 'price_rwf', 'discount_enabled', 'discount_type', 'discount_value', 'size', 'maxAdults', 'maxChildren', 'description']))
        var roomModal = document.getElementById('RoomModal');
        if (roomModal) {
            bootstrap.Modal.getOrCreateInstance(roomModal).show();
        }
    @endif

    function bindRoomDiscountFields(root) {
        root.querySelectorAll('[data-room-discount]').forEach(function (wrap) {
            var toggle = wrap.querySelector('[data-discount-toggle]');
            var typeEl = wrap.querySelector('[data-discount-type]');
            var valueLabel = wrap.querySelector('[data-discount-value-label]');
            var controls = wrap.querySelectorAll('[data-discount-controls]');

            function sync() {
                var on = toggle && toggle.checked;
                controls.forEach(function (el) { el.style.display = on ? '' : 'none'; });
                if (valueLabel && typeEl) {
                    valueLabel.textContent = typeEl.value === 'fixed' ? 'Amount off (USD)' : 'Percent off (%)';
                }
            }

            if (toggle) toggle.addEventListener('change', sync);
            if (typeEl) typeEl.addEventListener('change', sync);
            sync();
        });
    }
    bindRoomDiscountFields(document);

    var createForm = document.getElementById('roomCreateForm');
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            var nameEl = document.getElementById('roomName');
            var imageEl = document.getElementById('image');
            var name = nameEl ? nameEl.value.trim() : '';
            var hasImage = imageEl && imageEl.files && imageEl.files.length > 0;

            if (!name) {
                e.preventDefault();
                nameEl.classList.add('is-invalid');
                nameEl.focus();
                return;
            }
            nameEl.classList.remove('is-invalid');

            if (!hasImage) {
                e.preventDefault();
                imageEl.classList.add('is-invalid');
                imageEl.focus();
                return;
            }
            imageEl.classList.remove('is-invalid');
        });
    }

    $('#roomDescription').summernote({
        placeholder: 'Room description…',
        tabsize: 2,
        height: 220,
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
