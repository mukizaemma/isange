@extends('layouts.frontbase')

@section('content')
    @php
    $restaurant = App\Models\Restaurant::all()->first();
    // $images = json_decode($restaurant->image);
    @endphp

    <!-- Page Header Start -->
    <div class="container-fluid parallax-bg py-5 mb-5" style="background-image: url('{{asset('assets')}}/img/resto.jpg'); background-size: 100% 100%; background-position: center; object-fit:cover; width:800px;">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown text-center">Bar & Restaurant</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
    
                </ol>
            </nav>
        </div>
        </div>
        <!-- Page Header End -->

    {{-- <div class="container-xxl py-2"> --}}
        <div class="container">

            <div class="row g-1 portfolio-container">
                <div class="col-lg-12 portfolio-item first wow fadeInUp" data-wow-delay="0.1s">
                    <h3>{{ $restaurant->title }}</h3>
                    <p>{!! $restaurant->description !!}</p>                    
                </div>

            </div>

            <div class="row">
                @php
                $restaurant = App\Models\Restaurant::find(1); // Replace 1 with the ID of the restaurant you want to display
                $restaurant_id = $restaurant->id;
                $images = App\Models\Image::where('restaurant_id', $restaurant_id)->get();
                @endphp

                @if($images->count() > 0)
                    @foreach($images as $image)
                    <div class="col-lg-4 col-md-6 portfolio-item first wow fadeInUp" data-wow-delay="0.1s">
                        <div class="rounded overflow-hidden">
                            <div class="position-relative overflow-hidden">
                                <img class="img-fluid w-100" src="{{ asset('storage/images/images/' . $image->image) }}" alt="" style="height:250px;">
                                <div class="portfolio-overlay">
                                    <a class="btn btn-square btn-outline-light mx-1" href="{{ asset('storage/images/images/' . $image->image) }}" data-lightbox="portfolio"><i class="fa fa-eye"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif

            </div>
        </div>
    {{-- </div> --}}


{{-- @include('frontend.layouts.reservation') --}}
@endsection