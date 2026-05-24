    <!-- About Start -->
    @php
    $data = App\Models\About::first()
    @endphp
    @if($data)
    <div class="container-fluid bg-light overflow-hidden my-2 px-lg-0">
        <div class="container about px-lg-0">
            <div class="row g-0 mx-lg-0">
                <div class="col-lg-6 col-sm-12 ps-lg-0" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute img-fluid w-100 h-100" src="{{asset('storage/images/gallery/'.$data->aboutImage)}}" style="object-fit: cover;" alt="">
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12 feature-text wow fadeIn" data-wow-delay="0.5s" style="background-color: #f5f5f5; padding: 20px; border-radius: 8px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
                    <div class="p-lg-2 pe-lg-0">
                        <div class="section-title text-start" style="padding-left: 15px; margin-bottom: 15px;">
                            <h1 class="display-5 mb-1" style="font-family: 'Arial Black', sans-serif; color: #333;">Martin Aviator Hotel</h1>
                        </div>
                        <p class="mb-4 pb-2" style="padding-left: 10px; line-height: 1; color: #010101; font-size: 20px">
                            {!!$data->welcome!!}
                        </p>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
    @endif

    <!-- About End -->
