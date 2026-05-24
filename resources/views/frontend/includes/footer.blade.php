    <!-- Footer Start -->
    @php
    $data = App\Models\Setting::first()
    @endphp
    <div class="container-fluid text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-sm-12">
                    {{-- <h4 class="text-light mb-4">{{$data->company}}</h4> --}}
                    {{-- <img src="{{asset('assets')}}/img/white.png" alt="{{$data->company}}" style="width:150px;"> --}}
                    <img src="{{ asset('storage/images') . ($data->logo ?? '') }}" style="width:150px;">
                    <h5 class="text-light mt-4" style="font-family: 'MV Boli', sans-serif;">Touch point & Signature</h4>
                </div>
                <div class="col-lg-3 col-sm-12">
                    @php
                    $facilities = App\Models\Facility::all()
                    @endphp
                    <h4 class="text-light sm-12">Hotel Facilities</h4>
                    @foreach($facilities as $rs)
                    <a class="btn btn-link" href="">{{$rs->title}}</a>
                    @endforeach
                </div>

                <div class="col-lg-6 col-sm-12">
                    <h4 class="text-light mb-4">Get in touch</h4>
                    <p class="mb-2" style="color:#fff"><i class="fa fa-map-marker-alt me-3"></i>{{$data->address ?? ''}}</p>
                    <p class="mb-2" style="color:#fff"><i class="fa fa-phone-alt me-3"></i>{{$data->phone ?? ''}}</p>
                    <p class="mb-2" style="color:#fff"><i class="fa fa-envelope me-3"></i>{{$data->email ?? ''}}</p>
                    <div class="d-flex pt-2">
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="{{$data->facebook ?? ''}}" target="_blank" style="border-radius:7px;"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="{{$data->instagram ?? ''}}" target="_blank" style="border-radius:7px;"><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="{{$data->twitter ?? ''}}"  target="_blank" style="border-radius:7px;"><i class="fab fa-twitter" ></i></a>
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="{{$data->youtube ?? ''}}" target="_blank" style="border-radius:7px;"><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="https://api.whatsapp.com/send?phone={{$data->phone ?? ''}}&text=Hello" target="_blank" style="border-radius:7px;"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#"><script>document.write(new Date().getFullYear())</script> - {{$data->company ?? ''}}</a>, All Right Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                        Designed By <a class="border-bottom" href="https://iremetech.com">Ireme Technologies</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-0 back-to-top"><i class="bi bi-arrow-up"></i></a>
