@extends('layouts.frontbase')

@section('content')

    @include('frontend.includes.page-header', ['pageKey' => 'contact'])

        <section id="airport-transfer" class="contact-page-area py-100 rpy-80 rel z-1" tabindex="-1">
            <div class="container">
                <div class="row g-4 justify-content-center mb-55 wow fadeInUp delay-0-2s">
                    <div class="col-md-4">
                        <div class="contact-info-item justify-content-center flex-column text-center border rounded-3 py-4 px-3 h-100 bg-white shadow-sm">
                            <div class="icon mx-auto mb-15">
                                <i class="flaticon-location-1"></i>
                            </div>
                            <div class="content">
                                <span class="title d-block mb-1">Our location</span>
                                <span class="text">{{ $setting->address }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-info-item justify-content-center flex-column text-center border rounded-3 py-4 px-3 h-100 bg-white shadow-sm">
                            <div class="icon mx-auto mb-15">
                                <i class="flaticon-email-marketing"></i>
                            </div>
                            <div class="content">
                                <span class="title d-block mb-1">Email</span>
                                <span class="text">
                                    <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-info-item justify-content-center flex-column text-center border rounded-3 py-4 px-3 h-100 bg-white shadow-sm">
                            <div class="icon mx-auto mb-15">
                                <i class="flaticon-call"></i>
                            </div>
                            <div class="content">
                                <span class="title d-block mb-1">Phone</span>
                                <span class="text">
                                    <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}">{{ $setting->phone }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center mb-55 wow fadeInUp delay-0-2s">
                    <div class="col-xl-10 col-lg-11">
                        <div class="border rounded-3 p-4 p-md-5 bg-white shadow-sm">
                            <div class="section-title mb-25 text-center">
                                <span class="sub-title mb-10">Book or message us</span>
                                <h2 class="mb-0">Choose your channel</h2>
                                <p class="text-muted mt-3 mb-0">Book directly on our site, on a partner OTA, or reach the team on WhatsApp or email.</p>
                            </div>
                            @include('frontend.includes.booking-channels-grid')
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

        <div class="contact-page-map pb-120 rpb-90 wow fadeInUp delay-0-2s">
            <div class="container-fluid">
                <div class="our-location ma-map-embed">
                    @if (! empty($setting->google_map_embed))
                        {!! $setting->google_map_embed !!}
                    @else
                        <iframe src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d1282.6733953538446!2d29.34723022219446!3d-2.0577636813635376!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0x19dd291561adb953%3A0x6084750be3aaab83!2sDelta%20Resort%20Hotel%20Kibuye!3m2!1d-2.0575487!2d29.348358299999997!5e1!3m2!1sen!2srw!4v1738753640971!5m2!1sen!2srw" width="600" height="450" style="border:0;width: 100%;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    @endif
                </div>
            </div>
        </div>

        @include('frontend.includes.youtube-stories-widget', ['variant' => 'white'])

@endsection
