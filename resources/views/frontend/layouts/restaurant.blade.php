<div class="container-fluid bg-light overflow-hidden my-5 mt-10 px-lg-0">
    <div class="container feature px-lg-0">
        <div class="row g-0 mx-lg-0">
        @php
            $restaurant = App\Models\Restaurant::all()->first();
         @endphp
         @if($restaurant)
            <div class="col-lg-4 feature-text py-2 wow fadeIn" data-wow-delay="0.5s">
                <div class="mt-2">
                    {{-- <div class="section-title text-start"> --}}
                        <h4 class="display-5 mb-4">{{ $restaurant->title }}</h4>
                    {{-- </div> --}}


                    <p class="mb-4 p-2">{!! $restaurant->description !!}</p> 
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal{{ $restaurant->id }}">
                        View Details
                    </button>
                   

                </div>
            </div>
            <div class="col-lg-8 pe-lg-0" style="min-height: 400px;">
                <div class="position-relative h-100">
                    <img class="position-absolute img-fluid w-100 h-100" src="{{ asset('storage/images/' . $restaurant->cover) }}" style="object-fit: cover; max-height:500px; border-radius:7px;" alt="">
                </div>
            </div>

                <!-- The Modal -->
                <div class="modal fade" id="myModal{{ $restaurant->id }}">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">{{ $restaurant->title }}</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <!-- Display room details here -->
                                <p>{{ $restaurant->title }}</p>
                                @php
                                $restaurant = App\Models\Restaurant::find(1); // Replace 1 with the ID of the restaurant you want to display
                                $restaurant_id = $restaurant->id;
                                $images = App\Models\Image::where('restaurant_id', $restaurant_id)->get();
                                @endphp
                
                                @if($images->count() > 0)
                                    @foreach($images as $image)
                                    <div class="col-lg-4 col-md-6 portfolio-item first wow fadeInUp" data-wow-delay="0.1s">
                                        <div class="rounded overflow-hidden">
                                            <div class="position-relative overflow-hidden">
                                                <img class="img-fluid w-100" src="{{ asset('storage/images/images/' . $image->image) }}" alt="" style="height:250px;">
                                                <div class="portfolio-overlay">
                                                    <a class="btn btn-square btn-outline-light mx-1" href="{{ asset('storage/images/images/' . $image->image) }}" data-lightbox="portfolio"><i class="fa fa-eye"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
   
    </div>
</div>