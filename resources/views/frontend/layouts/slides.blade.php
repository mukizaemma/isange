    <!-- Carousel Start -->
    <div class="container-fluid p-0 pb-5">
        <div class="owl-carousel header-carousel position-relative" 
        style="max-width: 100%; margin: auto; overflow: hidden; background-color: #ffffff;">
        @foreach($slides as $rs)
        <div class="owl-carousel-item position-relative">
            <img 
                class="img-fluid" 
                src="{{ asset('storage/images/gallery') . $rs->image }}" 
                alt="" 
                style="width: 100%; max-height: 500px; object-fit: contain;">
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-8 text-center">
                            <h2 class="display-3 text-white mb-4 animate__animated animate__fadeInUp animate__delay-1s" 
                                style="
                                    font-weight: 700; 
                                    font-size: 40px; 
                                    color: #fff; 
                                    background-color: rgba(14, 39, 55, 0.452); 
                                    padding: 5px 20px; 
                                    display: inline-block; 
                                    border-radius: 10px; 
                                    line-height: 1.2;
                                "
                            >
                                {{ $rs->heading }}
                            </h2>
                            <div class="fs-5 fw-medium text-white mb-4 pb-2 animate__animated animate__fadeInUp animate__delay-2s">
                                <p>{{ $rs->caption }}</p>
                            </div>
                            <div>
                                <a href="{{ route('reserveNow') }}" class="btn btn-primary py-md-3 px-md-5 me-3 animate__animated animate__fadeInUp animate__delay-3s" style="border-radius:7px;">
                                    Make a Reservation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
        
    </div>

    <script>
        $(document).ready(function() {
            var owl = $('.owl-carousel');
            owl.owlCarousel({
                loop:true,
                margin:10,
                nav:true,
                dots:true,
                items:1,
                onTranslate: function(event) {
                    var currentItem = event.item.index;
                    var textItems = $('.owl-item').eq(currentItem).find('.text-animate');
                    textItems.removeClass('animated fadeInUp');
                    setTimeout(function(){
                        textItems.addClass('animated fadeInUp');
                    }, 100);
                }
            });
        });
        </script>


    <!-- Carousel End -->
