@extends('layouts.frontbase')

@section('content')


    <!-- Page Header Start -->
    <div class="container-fluid page-header parallax-bg py-5 mb-5" style="background-image: url('{{asset('assets')}}/img/1.jpg'); background-size: 100% 100%; background-position: center; object-fit:cover;">
        <div class="container py-5">
            <h1 class="display-3 text-black mb-3 animated slideInDown text-center">Our Programs</h1>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container-xxl py-50" style="margin-bottom:50px;">
    <div class="container">

        <div class="row g-4 portfolio-container">
            @foreach($programs as $rs)
            <div class="col-lg-4 col-sm-12 portfolio-item first wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded overflow-hidden">
                    <div class="position-relative overflow-hidden">
                        <img class="img-fluid " src="{{ asset('storage/images/programs/' . $rs->image) }}" alt="">
                        <div class="portfolio-overlay">
                            <a class="btn btn-square btn-outline-light mx-1" href="{{ asset('storage/images/programs/' . $rs->image) }}" data-lightbox="portfolio"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-square btn-outline-light mx-1" href=""><i class="fa fa-link"></i></a>
                        </div>
                    </div>
                    <div class="border border-5 border-light border-top-0 p-4">
                        <p class="text-primary fw-medium mb-2">{{$rs->title}}</p>
                        <h5 class="lh-base mb-0">Wooden Furniture Manufacturing And Remodeling</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>



@endsection
