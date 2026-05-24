@extends('layouts.frontbase')

@section('content')

    <!-- Page Header Start -->
    <div class="container-fluid page-header parallax-bg py-5 mb-5" style="background-image: url('{{asset('assets')}}/img/welcome.jpg'); background-size: 100% 100%; background-position: center; object-fit:cover;">
        <div class="container py-5">
            <div class="col-md-6">
                @if (session()->has('success'))
                <div class="arlert alert-success">
                    <button class="close" type="button" data-dismiss="alert">X</button>
                    {{ session()->get('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="arlert alert-danger">
                    <button class="close" type="button" data-dismiss="alert">X</button>
                    {{ session()->get('error') }}
                </div>
            @endif
            </div>

            @if ($availableTables->count() > 0)
            <h1 class="display-3 text-white mb-3 animated slideInDown text-center">Our Hotel Availability</h1><br>
            <p class="display-3 text-white mb-3 animated slideInDown text-center">From {{ $checkinDate }} to {{ $checkoutDate }}</p>
            @else
            <p class="display-3 text-red mb-3 animated slideInDown text-center">No availability from {{ $checkinDate }} to {{ $checkoutDate }}</p>             
            @endif
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
    
                </ol>
            </nav>
        </div>
        </div>
        <!-- Page Header End -->

<div class="container-xxl py-5  mb-50">
    <div class="container">

        <div class="row g-4 portfolio-container">
            @if($availableTables->count() >0)
            @foreach($availableTables as $rs)
            <div class="col-lg-4 col-md-6 portfolio-item first wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded overflow-hidden">
                    <div class="position-relative overflow-hidden">
                        <img class="img-fluid" src="{{ asset('storage/images/tables/' . $rs->image) }}" alt="" style="border-radius:7px;">
                        <div class="portfolio-overlay">
                            <a class="btn btn-square btn-outline-light mx-1" href="{{ asset('storage/images/images/' . $rs->image) }}" data-lightbox="portfolio"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-square btn-outline-light mx-1" href="{{ route('singleRoom', [$rs->slug, 'checkin'=>$checkinDate,'checkout'=>$checkoutDate]) }}"><i class="fa fa-link"></i></a>
                        </div>
                    </div>
                    <div class="border border-5 border-light border-top-0 p-4">
                        <h4 class="mb-3">{{ $rs->name }} </h4>
                        <div class="row">
                            <div class="col-sm-6">
                                <p>Starting from</p>
                                <span>$ {{ $rs->capacity }}/Max Seats </span>
                            </div>
                            <div class="col-sm-6">
                                <p>{{ $availableTables->count() }} Remaining</p>
                                <a class="fw-medium" href="#" data-bs-toggle="modal" data-bs-target="#bookNowModal{{ $rs->id }}">Book Now<i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="bookNowModal{{ $rs->id }}" tabindex="-1" aria-labelledby="bookNowModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="bookNowModalLabel">Reservation form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('confirmTableReservation', [$rs->id]) }}">
                                @csrf
                                <input type="hidden" name="table_id" value="{{ $rs->id }}">
                                <input type="hidden" name="tableName" value="{{ $rs->name }}">
                                <input type="hidden" name="checkin" value="{{ $checkinDate }}">
                                <input type="hidden" name="checkout" value="{{ $checkoutDate }}">

                                <div class="row mt-3">
                                    <div class="col-lg-12">
                                        <label for="name">Your Full Names</label>
                                        <input type="text" class="form-control" placeholder="Your Names" name="names" required="">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6 col-sm-6">
                                        <label for="name">Phone number</label>
                                        <input type="text" class="form-control" placeholder="Your Cell Phone No" name="phone" required="">
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="name">NUmber of People</label>
                                        <input type="number" name="people" class="form-controll" min="1" max="4" step="1" required="" oninput="checkValue1(this)">
                                        <script>
                                            function checkValue1(input) {
                                              if (input.value > input.max) {
                                                input.value = input.max;
                                                alert('This Table is only for 4 People);
                                              }
                                            }
                                            </script>
                
                                    </div>
                                </div>
                
                                <div class="row mt-3">
                                    <div class="col-lg-12">
                                         <label for="summernote" class="form-label">Any Please let us know if there any order you need?</label><br>
                                         <a href="{{route('OurMenu')}}" target="_blank">Check Our Menu</a>
                                            <textarea rows="5" class="form-control" name="description" placeholder="Please let us know if there is any other request"></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Confirm Your Reservation</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @else
            <div class="row">
                <form method="GET" action="{{ route('checkRooms') }}">

                  <div class="row mb-10">
                    <p class="text-center">Please select different dates</p>
                     <div class="col-lg-3">
                      <div class="form-group">
                        <label for="item">Select Item:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="item" id="room" value="Room" required>
                            <label class="form-check-label" for="room">Room</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="item" id="table" value="Table" required>
                            <label class="form-check-label" for="table">Table</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="item" id="paddle" value="Paddle" required>
                            <label class="form-check-label" for="paddle">Table</label>
                        </div>
                    </div>
                    </div>
                     <div class="col-lg-3">
                        <label for="checkin">Check In Date</label>
                        <input type="date" id="checkin" class="form-control" name="checkin" required="">
                    </div>
                    <div class="col-lg-3">
                        <label for="checkout">Check Out Date</label>
                        <input type="date" id="checkout" class="form-control" name="checkout" required="">
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary mt-2">Check Availability</button>
                    </div>
                  </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- @include('frontend.layouts.reservation') --}}
@endsection