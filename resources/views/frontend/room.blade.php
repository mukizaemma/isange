@extends('layouts.frontbase')

@section('content')

        <div class="container-fluid bg-light overflow-hidden my-2 px-lg-0">
            <div class="container about px-lg-0">
                <div class="container">
                    <div class="row">
                      <div class="col-sm-6">
                        <img src="path_to_your_full_width_image.jpg" class="full-width-img" alt="Full Width Image">
                      </div>
                      <div class="col-sm-6">
                        <div class="row">
                          <div class="col-6">
                            <img src="path_to_your_image1.jpg" class="grid-img" alt="Grid Image 1">
                          </div>
                          <div class="col-6">
                            <img src="path_to_your_image2.jpg" class="grid-img" alt="Grid Image 2">
                          </div>
                          <div class="col-6">
                            <img src="path_to_your_image3.jpg" class="grid-img" alt="Grid Image 3">
                          </div>
                          <div class="col-6">
                            <img src="path_to_your_image4.jpg" class="grid-img" alt="Grid Image 4">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
            </div>
        </div>

        <div class="container-xxl py-5  mb-50">
            <div class="container">
        
                <div class="row g-4 portfolio-container">
                    @foreach($images as $rs)
                    <div class="col-lg-3 col-md-6 portfolio-item first wow fadeInUp" data-wow-delay="0.1s">
                        <div class="rounded overflow-hidden">
                            <div class="position-relative overflow-hidden">
                                <img class="img-fluid w-100" src="{{ asset('storage/images/images') . $rs->image }}" alt="" style="height:200px;">
                                <div class="portfolio-overlay">
                                    <a class="btn btn-square btn-outline-light mx-1" href="{{ asset('storage/images/images/' . $rs->image) }}" data-lightbox="portfolio"><i class="fa fa-eye"></i></a>
                                    {{-- <a class="btn btn-square btn-outline-light mx-1" href=""><i class="fa fa-link"></i></a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="row">
                  <form class="form" action="{{ route('reserveNow') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px;">
                    @csrf
        
                    <div class="row">
                        <div class="col-xl-3 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Check-in Date</label>
                            <input type="date" name="checkin" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                        </div>
                        <div class="col-xl-3 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Check-out Date</label>
                            <input type="date" name="checkout" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                        </div>
                        <div class="col-xl-3 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Number of Guests</label>
                            <input type="number" name="adults" value="1" min="1" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                        <div class="col-xl-3 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Number of Rooms</label>
                            <input type="number" name="rooms" value="1" min="1" max="4" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                    </div>
        
                    {{-- <div class="row">
                        <div class="col-xl-4 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Number of Adults</label>
                            <input type="number" name="adults" value="1" min="1" max="3" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                        <div class="col-xl-4 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Number of Children</label>
                            <input type="number" name="children" value="1" min="1" max="3" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>

                    </div> --}}
        
                    <input type="text" name="names" placeholder="Your Names" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                    <input type="email" name="email" placeholder="Email" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                    <input type="text" name="phone" placeholder="Phone" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                    <input type="text" name="address" placeholder="Address, Country, City" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
        
                    <textarea name="description" placeholder="Any Special Request? (Optional)" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px; height: 80px;"></textarea>
        
                    <button type="submit" class="tp-btn" style="width: 100%; padding: 12px; border: none; border-radius: 4px; background-color: #007bff; color: white; font-size: 1rem; cursor: pointer;">Submit</button>
                </form>
                </div>
            </div>
            </div>

           
@endsection