@extends('layouts.frontbase')

    @section('content')
    @php
    $data = App\Models\About::first()
    @endphp

    @include('frontend.layouts.slides', ['slides'=>$slides])

    @include('frontend.layouts.aboutus')

    @include('frontend.layouts.services', ['services'=> $services])

    @include('frontend.layouts.rooms')

      
    @include('frontend.layouts.facilities', ['facilities'=> $facilities])


    <div class="container-fluid bg-light overflow-hidden my-2 px-lg-0 mt-3">
        <div class="container about px-lg-0">
            <div class="row g-0 mx-lg-0">
                @if(isset($adverts) && count($adverts) > 0)
                <div class="col-lg-6 col-sm-12">
                    <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                        <!-- Indicators/Dots -->
                        <div class="carousel-indicators">
                            @foreach($adverts as $index => $advert)
                                <button type="button" data-bs-target="#roomCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></button>
                            @endforeach
                        </div>
            
                        <!-- The Slideshow/Carousel -->
                        <div class="carousel-inner">
                            @foreach($adverts as $index => $advert)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/images/gallery/' . $advert->image) }}" alt="Advert Image {{ $index + 1 }}" class="d-block w-100 img-fluid">
                                </div>
                            @endforeach
                        </div>
            
                        <!-- Left and Right Controls -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="col-lg-6 col-sm-12">
                    <div class="position-relative h-100">
                        <p>No adverts available to display.</p>
                    </div>
                </div>
            @endif
                     
    
                
                <div class="col-lg-6 col-sm-12 wow fadeIn" data-wow-delay="0.5s">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d444.473895527913!2d30.143651493026137!3d-1.9953183282587776!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca9a7c2d20eb1%3A0x2a19d1cd1893c06c!2sBE%20Inn%20Hotel%20Kigali!5e0!3m2!1sen!2srw!4v1732804276146!5m2!1sen!2srw" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
    @endsection
