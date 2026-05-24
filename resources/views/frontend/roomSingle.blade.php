@extends('layouts.frontbase')
<base href="/public">

@section('content')

@include('frontend.includes.page-header', [
    'title' => $room->roomName,
    'subtitle' => null,
    'imageUrl' => ! empty($room->image)
        ? asset('storage/images/rooms/'.$room->image)
        : null,
])

    <!-- Room Details Start -->
    <section class="product-details pt-100 rpt-70 rel z-1">
        <div class="container">
            <div class="row gap-90">
                <div class="col-lg-6">
                    <div class="product-details-images wow fadeInLeft delay-0-2s">
                        <!-- Preview Images -->
                        <div class="tab-content preview-images">
                            <!-- Main Service Image -->
                            <div class="tab-pane fade preview-item active show" id="preview1">
                                <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="Preview">
                            </div>
                            
                            <!-- Gallery Images -->
                            @foreach ($images as $index => $image)
                                <div class="tab-pane fade preview-item" id="preview{{ $index + 2 }}">
                                    <img src="{{ asset('storage/images/rooms/' . $image->image) }}" alt="Preview">
                                </div>
                            @endforeach
                        </div>
            
                        <!-- Thumbnail Navigation -->
                        <div class="nav thumb-images rmb-20">
                            <!-- Main Service Image Thumbnail -->
                            <a href="#preview1" data-bs-toggle="tab" class="thumb-item active show">
                                <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="Thumb">
                            </a>
            
                            <!-- Gallery Thumbnails -->
                            @foreach ($images as $index => $image)
                                <a href="#preview{{ $index + 2 }}" data-bs-toggle="tab" class="thumb-item">
                                    <img src="{{ asset('storage/images/rooms/' . $image->image) }}" alt="Thumb">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="product-details-content mt-35 rmt-55 wow fadeInRight delay-0-2s">
                        <span class="price mb-20">{!! \App\Support\Currency::formatUsdOnly($room->price) !!} <span class="small">per night</span></span>
                        <ul class="ma-room-inclusions list-unstyled small mb-30">
                            <li><i class="fas fa-bath me-1"></i> Private bathroom &amp; hot shower</li>
                            <li><i class="fas fa-wifi me-1"></i> Free Wi-Fi</li>
                            <li><i class="fas fa-seedling me-1"></i> Balcony, terrace, or garden access</li>
                        </ul>
                        <p>
                            {!! $room->description !!}
                        </p>
                        @if ($room->amenityOptions->isNotEmpty())
                            <div class="room-amenities pt-3">
                                <h5 class="mb-3">Amenities</h5>
                                <ul class="list-unstyled row row-cols-1 row-cols-md-2 g-2 small">
                                    @foreach ($room->amenityOptions as $am)
                                        <li class="col"><i class="fas fa-check text-warning me-2"></i>{{ $am->label }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {{-- <form action="#" class="add-to-cart pt-5">
                            <input type="number" value="01" min="1" max="20" onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;" required>
                            <button type="submit" class="theme-btn">Add to Cart</button>
                        </form> --}}
                        <ul class="category-tags pt-55 pb-40">
                            <li>
                                <b>Category</b>
                                <span>:</span>
                                <a href="#">{{ $room->category }}</a>
                            </li>
                        </ul>
                        {{-- <div class="social-style-three">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div> --}}
                    </div>
                </div>
            </div>

        </div>
        <div class="bg-lines for-bg-white">
           <span></span><span></span>
           <span></span><span></span>
           <span></span><span></span>
           <span></span><span></span>
           <span></span><span></span>
        </div>
    </section>
    <!-- Room Details End -->

    <div class="container py-5 mb-50">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Book this room</h3>
                <p class="text-muted mb-4">Pay directly when available, or choose WhatsApp, email, or a partner site — all options are on our booking page.</p>
                <a href="{{ route('pay.dpo', ['room' => $room->slug]) }}" class="theme-btn">Book Now <i class="far fa-angle-right ms-2"></i></a>
                <a href="{{ route('room.booking', ['room' => $room->slug]) }}" class="theme-btn style-three ms-2 mt-2 mt-sm-0 d-inline-block">Other ways to book</a>
                <a href="{{ route('rooms') }}" class="theme-btn style-three ms-2 mt-2 mt-sm-0 d-inline-block">All accommodation</a>
            </div>
        </div>
    </div>
    

@endsection