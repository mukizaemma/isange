@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'pageKey' => 'rooms',
    'title' => $room->roomName,
    'imageUrl' => ! empty($room->image)
        ? asset('storage/images/rooms/'.$room->image)
        : null,
])

<section class="product-details pt-100 rpt-70 pb-80 rel z-1">
    <div class="container">
        <div class="row gap-90">
            <div class="col-lg-6">
                <div class="product-details-images wow fadeInLeft delay-0-2s">
                    <div class="tab-content preview-images">
                        <div class="tab-pane fade preview-item active show" id="preview1">
                            <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="{{ $room->roomName }}">
                        </div>
                        @foreach ($images as $index => $image)
                            <div class="tab-pane fade preview-item" id="preview{{ $index + 2 }}">
                                <img src="{{ asset('storage/images/rooms/' . $image->image) }}" alt="{{ $room->roomName }}">
                            </div>
                        @endforeach
                    </div>
                    @if ($images->isNotEmpty())
                        <div class="nav thumb-images rmb-20">
                            <a href="#preview1" data-bs-toggle="tab" class="thumb-item active show">
                                <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="Thumb">
                            </a>
                            @foreach ($images as $index => $image)
                                <a href="#preview{{ $index + 2 }}" data-bs-toggle="tab" class="thumb-item">
                                    <img src="{{ asset('storage/images/rooms/' . $image->image) }}" alt="Thumb">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="product-details-content mt-35 rmt-55 wow fadeInRight delay-0-2s">
                    <h2 class="mb-4">{{ $room->roomName }}</h2>
                    @if (! empty(trim(strip_tags($room->description ?? ''))))
                        <div class="room-description prose">
                            {!! $room->description !!}
                        </div>
                    @endif
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="button" class="theme-btn"
                            data-add-room
                            data-room-id="{{ $room->id }}"
                            data-room-slug="{{ $room->slug }}"
                            data-room-name="{{ $room->roomName }}"
                            data-room-price="{{ $room->price }}"
                            data-room-image="{{ asset('storage/images/rooms/' . $room->image) }}">
                            Add to stay cart
                        </button>
                        <a href="{{ route('booking.checkout', ['room' => $room->slug]) }}" class="theme-btn style-three">Book now</a>
                    </div>
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

@endsection
