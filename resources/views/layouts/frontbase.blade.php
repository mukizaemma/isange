@php
    $brandLogo = ! empty($setting->logo ?? null)
        ? asset('storage/images/' . ltrim($setting->logo, '/'))
        : asset('assets/images/isange-logo.png');
    $globalContent = \App\Support\PageContent::get('global', $pageHeaders ?? collect());
    $globalSections = $globalContent['sections'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->

    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-JFJE662BHZ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-JFJE662BHZ');
</script>

    <meta charset="utf-8">
    <meta name="description" content="{{ $setting->keywords ?? '' }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#106b38">
    <meta name="spa-site-name" content="{{ e($setting->company ?? config('app.name')) }}">

    <!-- Title -->
    <title>{{ $setting->company ?? '' }}</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    
    <!-- Flaticon -->
    <link rel="stylesheet" href="{{ asset('assets/css/flaticon.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome-5.14.0.min.css') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
    <!-- Nice Select -->
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.min.css') }}">
    <!-- Type Writer -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.animatedheadline.css') }}">
    <!-- Animate -->
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
    <!-- Slick -->
    <link rel="stylesheet" href="{{ asset('assets/css/slick.min.css') }}">
    <!-- Main Style -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/brand-isange.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/isange-home.css') }}">

</head>
<body class="home-one">
    <div class="page-wrapper">

        <!-- main header -->
        <header class="main-header">
           <div class="header-top-wrap bgc-primary">
               <div class="container">
                   <div class="header-top-single">
                       <div class="header-contact-inline">
                           <ul class="header-contact-list">
                               <li>
                                   <i class="fas fa-phone" aria-hidden="true"></i>
                                   <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}">{{ $setting->phone ?? '' }}</a>
                               </li>
                               <li>
                                   <i class="fas fa-envelope" aria-hidden="true"></i>
                                   <a href="mailto:{{ $setting->email ?? '' }}">{{ $setting->email ?? '' }}</a>
                               </li>
                           </ul>
                       </div>
                       <p class="header-location-tagline">
                           <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                           <span>{{ $globalSections['header_tagline'] ?? 'Musanze, Rwanda — 15 minutes drive from/to Volcanoes National Park office' }}</span>
                       </p>
                       <div class="header-social-inline">
                           @include('frontend.includes.social-links', ['style' => 'two'])
                       </div>
                   </div>
               </div>
           </div>
           
            <!--Header-Upper-->
            <div class="header-upper">
                <div class="container clearfix">

                    <div class="header-inner header-inner--balanced rel align-items-center">
                        <div class="logo-outer">
                            <div class="logo"><a href="{{route('home')}}"><img src="{{ $brandLogo }}" alt="{{ $setting->company ?? 'Isange Paradise Eco Resort' }}" title="{{ $setting->company ?? '' }}" style="height: 60px !important"></a></div>
                        </div>

                        <div class="nav-outer clearfix">
                            <!-- Main Menu -->
                            <nav class="main-menu navbar-expand-lg">
                                <div class="navbar-header">
                                   <div class="mobile-logo my-15">
                                       <a href="{{route('home')}}">
                                            <img src="{{ $brandLogo }}" alt="{{ $setting->company ?? 'Isange Paradise Eco Resort' }}" title="{{ $setting->company ?? '' }}" style="height: 60px !important">
                                       </a>
                                   </div>
                                   
                                    <!-- Toggle Button -->
                                    <button type="button" class="navbar-toggle" data-bs-toggle="collapse" data-bs-target=".navbar-collapse">
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                </div>

                                <div class="navbar-collapse collapse clearfix">

                                    <ul class="navigation clearfix">
                                        <li><a href="{{ route('home') }}">Home</a></li>
                                        <li class="dropdown"><a href="{{ route('aboutUs') }}">About</a>
                                            <ul>
                                                <li><a href="{{ route('services') }}">Our Services</a></li>
                                                <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                                                <li><a href="{{ route('blogs') }}">Updates</a></li>
                                            </ul>
                                        </li>
                                        @include('frontend.includes.nav-accommodation')
                                        <li class="dropdown"><a href="{{ route('facilities') }}">Facilities</a>
                                            <ul>
                                                <li><a href="{{ route('dining') }}">Restaurant &amp; Bar</a></li>
                                                <li><a href="{{ route('facilities') }}#mic">Meeting &amp; Conference (MIC)</a></li>
                                                <li><a href="{{ route('facilities') }}#garden">Garden</a></li>
                                                <li><a href="{{ route('future4kids') }}#shop">Future 4 Kids Shop</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="{{ route('experiences') }}">Experiences</a></li>
                                        <li><a href="{{ route('future4kids') }}">Future 4 Kids</a></li>
                                        <li><a href="{{ route('blogs') }}">Updates</a></li>
                                        <li><a href="{{ route('gallery') }}">Gallery</a></li>
                                    </ul>
                                </div>

                            </nav>
                            <!-- Main Menu End-->
                        </div>
                        
                        
                        <!-- Menu Button -->
                        <div class="menu-btns">
                           @php($headerBookingUrl = \App\Support\BookingEngine::url($setting) ?? route('booking.checkout'))
                           <a
                               href="{{ $headerBookingUrl }}"
                               class="theme-btn style-three"
                               @if (\App\Support\BookingEngine::isConfigured($setting)) target="_blank" rel="noopener noreferrer" @endif
                           >Book Your Stay <i class="far fa-angle-right"></i></a>
                           
                            <!-- menu sidbar -->
                            {{-- <div class="menu-sidebar">
                                <button>
                                    <img src="assets/images/icons/sidebar-toggler-color.png" alt="Toggler">
                                </button>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <!--End Header Upper-->
        </header>
       
       
    <div class="container-fluid" id="spa-content" data-spa-container>
        @fragment('spa-main')
        @yield('content')
        @endfragment
    </div>

        @include('frontend.includes.amenities-band')

        @include('frontend.includes.footer-partners')
       
        <!-- footer area start -->
        <footer class="main-footer bgc-black pt-100 rel z-1 ma-footer-gold">
            <div class="container">
                <div class="row align-items-start g-4 g-xl-5 ma-footer-main-row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget widget_about wow fadeInUp delay-0-2s">
                            <div class="footer-logo mb-25">
                                <a href="{{route('home')}}"><img src="{{ $brandLogo }}" alt="{{ $setting->company ?? 'Isange Paradise Eco Resort' }}" style="height: 80px !important"></a>
                            </div>
                            <p>
                                {{ $globalSections['footer_blurb'] ?? 'Your sustainable escape — 15 minutes drive from/to Volcanoes National Park office. Comfort, nature, and purpose — every stay supports community development through Future 4 Kids.' }}
                            </p>

                            <ul class="contact-list">
                                <li><i class="fas fa-phone-alt"></i> <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}">{{ $setting->phone ?? '' }}</a></li>
                                <li><i class="fas fa-envelope"></i> <a href="mailto:{{ $setting->email ?? '' }}">{{ $setting->email ?? '' }}</a></li>
                            </ul>
                            <div class="pt-10">
                                @include('frontend.includes.social-links', ['style' => 'one'])
                            </div>
                            @include('frontend.includes.footer-map-card')
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget widget_nav_menu wow fadeInUp delay-0-4s">
                            <h4 class="footer-title">Quick Links</h4>
                            <ul class="list-style-one mb-3">
                                <li><a href="{{ route('rooms') }}">Accommodation</a></li>
                                <li><a href="{{ route('experiences') }}">Experiences</a></li>
                                <li><a href="{{ route('dining') }}">Restaurant &amp; Bar</a></li>
                                <li><a href="{{ route('future4kids') }}">Future 4 Kids</a></li>
                                <li><a href="{{ route('blogs') }}">Updates</a></li>
                                <li><a href="{{ route('contact') }}">Contact</a></li>
                                <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                            </ul>

                        </div>
                    </div>
                    <div class="col-lg-6">
                        @include('frontend.includes.booking-channels-grid', ['compact' => false, 'context' => 'footer'])
                        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <h4 class="section-title-sm font-weight-bold mb-3">Payment methods</h4>
                            <img src="{{ asset('assets/images/payment1.png') }}" alt="Accepted cards and mobile money" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom bgd-dark mt-40 pt-20 pb-5 rpt-25">
                <div class="container">
                   <div class="row">
                       <div class="col-lg-6">
                            <div class="copyright-text">
                                <p>©  <script>document.write(new Date().getFullYear())</script> <a href="{{route('home')}}">{{ $setting->company }}</a> All Rights Reserved.</p>
                            </div>
                       </div>
                       <div class="col-lg-6 text-lg-end">
                           <ul class="footer-bottom-nav rpb-10">
                               <li><a href="https://iremetech.com" target="_blank">Developed by Ireme Technologies</a></li>
                               <li><a  target="_blank"></a></li>

                           </ul>
                       </div>
                   </div>
                </div>
            </div>
            <div class="bg-lines">
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
            </div>
        </footer>
        <!-- footer area end -->
        
        
        @include('frontend.includes.whatsapp-float')

        <!-- Scroll Top Button -->
        <button class="scroll-top scroll-to-target" data-target="html"><span class="fas fa-angle-double-up"></span></button>

    </div>
    <!--End pagewrapper-->

    {{-- Modals outside .page-wrapper — avoids overflow:hidden clipping and backdrop stacking issues --}}
    @stack('body-modals')

    {{-- Outside .page-wrapper so overflow:hidden does not clip the fixed cart bar --}}
    @include('frontend.includes.stay-cart-dock')
   
    
    <!-- Jquery -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}" defer></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}" defer></script>
    <!-- Appear Js -->
    <script src="{{ asset('assets/js/appear.min.js') }}" defer></script>
    <!-- Slick -->
    <script src="{{ asset('assets/js/slick.min.js') }}" defer></script>
    <!-- Magnific Popup -->
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}" defer></script>
    <!-- Nice Select -->
    <script src="{{ asset('assets/js/jquery.nice-select.min.js') }}" defer></script>
    <!-- Image Loader -->
    <script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}" defer></script>
    <!-- Calendar -->
    <script src="{{ asset('assets/js/calendar.global.min.js') }}" defer></script>
    <!-- Circle Progress -->
    <script src="{{ asset('assets/js/circle-progress.min.js') }}" defer></script>
    <!-- Isotope -->
    <script src="{{ asset('assets/js/isotope.pkgd.min.js') }}" defer></script>
    <!--  WOW Animation -->
    <script src="{{ asset('assets/js/wow.min.js') }}" defer></script>
    <!-- Custom script -->
    <script src="{{ asset('assets/js/script.js') }}" defer></script>
    <script src="{{ asset('assets/js/dual-currency.js') }}" defer></script>
    <script src="{{ asset('assets/js/parallax-bg.js') }}" defer></script>
    <script src="{{ asset('assets/js/stay-booking-cart.js') }}" defer></script>
    {{-- SPA navigation disabled: normal link clicks for reliable navigation and cart/scripts on each page --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.remove('spa-loading');
            window.dispatchEvent(new Event('ma:spa-content'));
        });
        (function () {
            function purgeStuckOverlays() {
                if (document.querySelectorAll('.modal.show').length > 0) {
                    return;
                }
                document.querySelectorAll('.modal-backdrop').forEach(function (el) {
                    el.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }
            document.addEventListener('hidden.bs.modal', purgeStuckOverlays);
            window.addEventListener('pageshow', purgeStuckOverlays);
        })();
    </script>

    @stack('scripts')
    @yield('scripts')

</body>
</html>