@extends('layouts.frontbase')
<base href="/public">

@section('content')

@include('frontend.includes.page-header', [
    'title' => $facility->title,
    'subtitle' => null,
    'imageUrl' => ! empty($facility->image)
        ? asset('storage/images/facilities/'.$facility->image)
        : null,
])

<!-- Room Details Start -->
<section class="product-details pt-100 mb-120 rpt-70 rel z-1">
    <div class="container">
        <div class="row gap-90">
            <div class="col-lg-6">
                <div class="product-details-images wow fadeInLeft delay-0-2s">
                    <!-- Preview Images -->
                    <div class="tab-content preview-images">
                        <!-- Main Service Image -->
                        <div class="tab-pane fade preview-item active show" id="preview1">
                            <img src="{{ asset('storage/images/facilities/' . $facility->image) }}" alt="Preview">
                        </div>
                        
                        <!-- Gallery Images -->
                        @foreach ($images as $index => $image)
                            <div class="tab-pane fade preview-item" id="preview{{ $index + 2 }}">
                                <img src="{{ asset('storage/images/facilities/' . $image->image) }}" alt="Preview">
                            </div>
                        @endforeach
                    </div>
        
                    <!-- Thumbnail Navigation -->
                    <div class="nav thumb-images rmb-20">
                        <!-- Main Service Image Thumbnail -->
                        <a href="#preview1" data-bs-toggle="tab" class="thumb-item active show">
                            <img src="{{ asset('storage/images/facilities/' . $facility->image) }}" alt="Thumb">
                        </a>
        
                        <!-- Gallery Thumbnails -->
                        @foreach ($images as $index => $image)
                            <a href="#preview{{ $index + 2 }}" data-bs-toggle="tab" class="thumb-item">
                                <img src="{{ asset('storage/images/facilities/' . $image->image) }}" alt="Thumb">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="product-details-content mt-35 rmt-55 wow fadeInRight delay-0-2s">
                    <p>
                        {!! $facility->description !!}
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>
<!-- Room Details End -->

<section class="shop-page-area py-30 rpy-95 rel z-1">
    <div class="container">    
        <div class="row justify-content-center">
            @foreach ($reletedFacilities as $relFacility)

            <div class="col-xl-4 col-md-6">
                <div class="product-item wow fadeInUp delay-0-2s">
                    <div class="image">
                        <img src="{{ asset('storage/images/facilities/' . $relFacility->image) }}" alt="{{ $relFacility->title }}" style="height: 350px; object-fit: cover;">
                        <div class="social-style-one">
                            <a href="{{ route('facilitySingle',['slug'=>$relFacility->slug]) }}"><i class="far fa-eye"></i></a>
                        </div>
                    </div>
                    <div class="content">
                        <h4><a href="{{ route('facilitySingle',['slug'=>$relFacility->slug]) }}">{{ $relFacility->title }}</a></h4>
                    </div>
                </div>
            </div>
            @endforeach
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