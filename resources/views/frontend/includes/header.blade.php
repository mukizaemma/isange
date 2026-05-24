    <!-- Topbar Start -->
    @php
    $data = App\Models\Setting::first()
    @endphp
    <div class="container-fluid p-0" style="background-color:#000101;color:#fff">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-map-marker-alt text-primary me-2"></small>
                    <small>{{$data->address ?? ''}}</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center py-3">
                    <small class="far fa-clock text-primary me-2"></small>
                    <small>Open : 24/7</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-phone-alt text-primary me-2"></small>
                    <small>{{$data->phone ?? ''}}</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                <a class="btn btn-sm-square bg-white text-primary me-1" href="{{$data->facebook ?? ''}}" target="_blank" style="border-radius:7px;"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="{{$data->instagram ?? ''}}" target="_blank" style="border-radius:7px;"><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="{{$data->twitter ?? ''}}"  target="_blank" style="border-radius:7px;"><i class="fab fa-twitter" ></i></a>
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="{{$data->youtube ?? ''}}" target="_blank" style="border-radius:7px;"><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="https://api.whatsapp.com/send?phone={{$data->phone ?? ''}}&text=Hello" target="_blank" style="border-radius:7px;"><i class="fab fa-whatsapp"></i></a>
                </div>

            </div>

        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top p-0" style="background-color: #019A01">
        <a href="{{route('home')}}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            {{-- <h2 class="m-0 text-primary">{{$data->company}}</h2> --}}
            <img src="{{ asset('storage/images') . ($data->logo ?? '') }}" alt="" style="max-height:70px;"><br>
            <!-- <h5 class="mt-4" style="font-family: 'Forte', sans-serif; color:#134045">Touch point & Signature</h4> -->
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="{{route('home')}}" class="nav-item nav-link active">Home</a>
                <a href="{{route('services')}}" class="nav-item nav-link">Services</a>
                <a href="{{route('rooms')}}" class="nav-item nav-link">Hotel Rooms</a>
                <a href="{{route('facilities')}}" class="nav-item nav-link">Our Facilities</a>
                <a href="{{route('gallery')}}" class="nav-item nav-link">Gallery</a>
                {{-- <a href="{{route('gallery')}}" class="nav-item nav-link">Updates</a> --}}
                <a href="{{route('contact')}}" class="nav-item nav-link">Contact</a>
            </div>
            <a href="{{route('rooms')}}" class="btn btn-secondary py-4 px-lg-2 d-none d-lg-block">Reserve Now<i class="fa fa-arrow-right ms-3"></i></a>
            
            {{-- @if(Route::has('login'))

            @auth
            <x-app-layout>

            </x-app-layout>
            
            @else
            <a href="">Login</a>
            <a href="">Register</a>
            @endauth
            @endif --}}
            
        </div>
    </nav>
    <!-- Navbar End -->
