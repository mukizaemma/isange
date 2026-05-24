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

            @if ($availableRooms->count() > 0)
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
            @if($availableRooms->count() > 0)
            @foreach($availableRooms as $availableRoom)
                @php
                    $date = $availableRoom['date'];
                    $room = $availableRoom['room'];
                @endphp
                
                <div class="col-lg-4 col-md-6 portfolio-item first wow fadeInUp" data-wow-delay="0.1s">
                    <div class="rounded overflow-hidden">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="" style="border-radius:7px;">
                            <div class="portfolio-overlay">
                                <a class="btn btn-square btn-outline-light mx-1" href="{{ asset('storage/images/images/' . $room->image) }}" data-lightbox="portfolio"><i class="fa fa-eye"></i></a>
                                <a class="btn btn-square btn-outline-light mx-1" href="{{ route('singleRoom', [$room->slug, 'checkin'=>$checkinDate,'checkout'=>$checkoutDate]) }}"><i class="fa fa-link"></i></a>
                            </div>
                        </div>
                        <div class="border border-5 border-light border-top-0 p-4">
                            <h4 class="mb-3">{{ $room->roomName }} </h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p>Starting from</p>
                                    <span>$ {{ $room->singlePrice }}/Night </span>
                                </div>
                                <div class="col-sm-6">
                                    <p>{{ $room->quantity }} Remaining</p>
                                    <a class="fw-medium" href="#" data-bs-toggle="modal" data-bs-target="#bookNowModal{{ $room->id }}">Book Now<i class="fa fa-arrow-right ms-2"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal fade" id="bookNowModal{{ $room->id }}" tabindex="-1" aria-labelledby="bookNowModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="bookNowModalLabel">Reservation form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('confirmReservation', [$room->id]) }}">
                                @csrf
                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                <input type="hidden" name="price" value="{{ $room->singlePrice }}">
                                <input type="hidden" name="room" value="{{ $room->roomName }}">
                                <input type="hidden" name="checkin" value="{{ $checkinDate }}">
                                <input type="hidden" name="checkout" value="{{ $checkoutDate }}">

                                <div class="row mt-3">
                                    <div class="col-lg-12">
                                        <label for="name">Your Full Names</label>
                                        <input type="text" class="form-control" placeholder="Your Names" name="names" required="">
                                    </div>
                                    <div class="col-6 col-sm-6">
                                        <label for="name">Email</label>
                                        <input type="text" class="form-control" placeholder="Your Email" name="email" required="">
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <label for="name">Phone number</label>
                                        <input type="text" class="form-control" placeholder="Your Cell Phone No" name="phone" required="">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-2 col-md-6 col-sm-6">
                                        <label for="name">Adults</label>
                                        <input type="number" name="adults" class="form-controll" min="1" max="2" step="1" required="" oninput="checkValue1(this)">
                                        <script>
                                            function checkValue1(input) {
                                              if (input.value > input.max) {
                                                input.value = input.max;
                                                alert('This room does not accommodate more than 2 Adults. Please select ' + input.min + ' Or ' + input.max + '.');
                                              }
                                            }
                                            </script>
                
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6">
                                        <label for="name">Select</label>
                                        <input type="number" name="children" class="form-controll" min="0" max="1" step="1" required="" oninput="checkValue(this)">
                                        <script>
                                            function checkValue(input) {
                                              if (input.value > input.max) {
                                                input.value = input.max;
                                                alert('This room only accommodates 1 extra child. Please put in the message field if you have more than one child so that we can find out more ways to help you! ');
                                              }
                                            }
                                            </script>
                                    </div>
                                </div>
                
                                <div class="row">
                                    <div class="col-lg-12">
                                         <label for="summernote" class="form-label">Any Other Request?</label>
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