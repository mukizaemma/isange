<section class="shop-page-area py-10 rpy-95 rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            @foreach ($services as $service )

            <div class="col-xl-4 col-md-6">
                <div class="product-item wow fadeInUp delay-0-2s">
                    <div class="image">
                        <img src="{{ asset('storage/images/services/' . $service->image) }}" alt="{{ $service->title }}" style="height: 350px; object-fit: cover;">
                        <div class="social-style-one">
                            <a href="{{ route('singleService',['slug'=>$service->slug]) }}"><i class="far fa-eye"></i></a>
                        </div>
                    </div>
                    <div class="content">
                        <h4><a href="{{ route('singleService',['slug'=>$service->slug]) }}">{{ $service->title }}</a></h4>
                    </div>
                </div>
            </div>
            @endforeach
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