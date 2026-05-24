<!-- Facilities Start -->
<div class="container-xxl py-5 mt-30">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h1 class="display-5">Our Facilities</h1>
            <p class="text-muted">Eco-friendly spaces designed for comfort, events, and unforgettable stays near Volcanoes National Park</p>
        </div>
        <div class="row g-4">
            @foreach($facilities as $rs)
            <div class="col-lg-4 col-sm-12 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card border-0 shadow-sm h-100">
                    <div class="position-relative overflow-hidden">
                         <a href="{{ route('facilitySingle',['slug'=>$rs->slug]) }}">
                            <img class="card-img-top" src="{{ asset('storage/images/facilities/' . $rs->image) }}" alt="{{ $rs->title }}" style="object-fit: cover; height: 250px;">
                         </a>
                        <div class="overlay bg-dark position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="opacity: 0; transition: opacity 0.4s;">
                            <p class="text-white fw-bold"> <a href="{{ route('facilitySingle',['slug'=>$rs->slug]) }}">{{ $rs->title }}</a></p>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title mb-3">{{ $rs->title }}</h5>
                        <p class="card-text text-muted" style="font-size: 1rem;">{!! $rs->description !!}</p>
                    </div>
                    <div class="card-footer text-center bg-light border-top-0">
                        <a href="{{ route('facilitySingle',['slug'=>$rs->slug]) }}" class="btn btn-outline-primary btn-sm rounded-pill">Learn More</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Facilities End -->
