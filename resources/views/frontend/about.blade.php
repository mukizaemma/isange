@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'about'])

<section class="who-we-are-area pb-130 rpb-100 rel z-1 isange-section--cream">
    <div class="container">
        <div class="row justify-content-between align-items-center g-4">
            <div class="col-xl-6 col-lg-7">
                <div class="who-we-are-content wow fadeInUp delay-0-2s">
                    <span class="isange-section__eyebrow">Our story</span>
                    <div class="section-title mb-35">
                        <h2>Welcome to Isange Paradise</h2>
                        <p>
                            Isange Paradise is more than a resort—it is a social enterprise designed to create lasting impact in Musanze, one of Rwanda’s top tourist destinations.
                        </p>
                        <p>
                            Owned by <strong>Future 4 Kids</strong>, our eco-resort provides meaningful employment, supports vulnerable families, and funds education, healthcare, skills development, and women’s empowerment.
                        </p>
                        @if (! empty($about->welcome))
                            <div class="welcome-prose mt-3">{!! $about->welcome !!}</div>
                        @endif
                    </div>
                    <a class="theme-btn" href="{{ route('future4kids') }}">Our impact mission <i class="far fa-angle-right"></i></a>
                </div>
            </div>
            @if (! empty($about->aboutImage))
            <div class="col-lg-5">
                <div class="isange-about-media wow fadeInUp delay-0-4s">
                    <img src="{{ asset('storage/images/gallery/' . ltrim($about->aboutImage, '/')) }}" alt="Isange Paradise Eco Resort" loading="lazy">
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<section class="isange-section rel z-1 bgc-white" id="team">
    <div class="container">
        <div class="row justify-content-center text-center mb-45">
            <div class="col-lg-8 wow fadeInUp">
                <span class="isange-section__eyebrow">Our team</span>
                <h2>Warm Rwandan Hospitality</h2>
                <p class="mb-0">Our team brings authentic care, local knowledge, and a commitment to sustainable tourism — so your stay is comfortable, memorable, and meaningful.</p>
            </div>
        </div>
        @if (! empty($about->chooseUs))
            <div class="row justify-content-center wow fadeInUp">
                <div class="col-lg-10 welcome-prose">{!! $about->chooseUs !!}</div>
            </div>
        @endif
    </div>
</section>

@include('frontend.layouts.terms')

@include('frontend.includes.youtube-stories-widget', ['variant' => 'white'])

@endsection
