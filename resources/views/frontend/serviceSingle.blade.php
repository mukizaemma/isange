@extends('layouts.frontbase')
<base href="/public">

@section('content')

@include('frontend.includes.page-header', [
    'title' => $service->title,
    'subtitle' => null,
    'imageUrl' => ! empty($service->image)
        ? asset('storage/images/services/'.$service->image)
        : null,
])

    <!-- Room Details Start -->
    <section class="product-details pt-100  mb-30 rpt-70 rel z-1">
        <div class="container">
            <div class="row gap-90">
                <div class="col-lg-6">
                    <div class="product-details-images wow fadeInLeft delay-0-2s">
                        <!-- Preview Images -->
                        <div class="tab-content preview-images">
                            <!-- Main Service Image -->
                            <div class="tab-pane fade preview-item active show" id="preview1">
                                <img src="{{ asset('storage/images/services/' . $service->image) }}" alt="Preview">
                            </div>
                            
                            <!-- Gallery Images -->
                            @foreach ($images as $index => $image)
                                <div class="tab-pane fade preview-item" id="preview{{ $index + 2 }}">
                                    <img src="{{ asset('storage/images/services/' . $image->image) }}" alt="Preview">
                                </div>
                            @endforeach
                        </div>
            
                        <!-- Thumbnail Navigation -->
                        <div class="nav thumb-images rmb-20">
                            <!-- Main Service Image Thumbnail -->
                            <a href="#preview1" data-bs-toggle="tab" class="thumb-item active show">
                                <img src="{{ asset('storage/images/services/' . $service->image) }}" alt="Thumb">
                            </a>
            
                            <!-- Gallery Thumbnails -->
                            @foreach ($images as $index => $image)
                                <a href="#preview{{ $index + 2 }}" data-bs-toggle="tab" class="thumb-item">
                                    <img src="{{ asset('storage/images/services/' . $image->image) }}" alt="Thumb">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            
                <div class="col-lg-6">
                    <div class="product-details-content mt-35 rmt-55 wow fadeInRight delay-0-2s">
                        <p>
                            {!! $service->description !!}
                        </p>
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


    @include('frontend.layouts.gallery')

@endsection