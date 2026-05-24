@extends('layouts.frontbase')

@section('content')
    @php
    $restaurant = App\Models\Reservepolocy::all()->first();
    // $images = json_decode($restaurant->image);
    @endphp

    <!-- Page Header Start -->

        <!-- Page Header End -->

    {{-- <div class="container-xxl py-2"> --}}
        <div class="container pt-20">

            <div class="row g-1 portfolio-container">
                <div class="col-lg-12 portfolio-item first wow fadeInUp" data-wow-delay="0.1s">
                    <h3>{{ $restaurant->title }}</h3>
                    <p>{!! $restaurant->description !!}</p>                    
                </div>

            </div>

            <div class="row" style="padding: 24px; background-color: #f9f9f9; color: #333;">
                <h3>Reserve Your Stay now</h3>
                <form class="form" action="{{ route('reserveNow') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px;">
                    @csrf
        
                    <div class="row">
                        <div class="col-lg-4 clo-sm-12">
                            <label for="">Selecte a Room Category</label>
                            <select name="room_id" id="">
                                <option value="" disabled> --Select Room--</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->roomName }} - ${{ $room->singlePrice }}</option>
                                @endforeach
                            </select>
                        </div>
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
                
                    <div class="row">
                        <div class="col-lg-6 col-sm">
                            <input type="text" name="names" placeholder="Your Names" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                        <div class="col-lg-6 col-sm">
                            <input type="email" name="email" placeholder="Email" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <input type="text" name="phone" placeholder="Phone" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <input type="text" name="address" placeholder="Address, Country, City" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                    </div>
        
                    <textarea name="description" placeholder="Any Special Request? (Optional)" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px; height: 80px;"></textarea>
        
                    <div class="row d-flex justify-content-center align-items-center gap-2" style="padding: 5px;">
                        <button type="submit" class="tp-btn btn btn-primary" style="width:20%; padding: 12px; border-radius: 4px; font-size: 1rem;">Submit</button>
                        <a href="{{route('rooms')}}" class="btn btn-secondary" style="width:20%; padding: 12px; border-radius: 4px; font-size: 1rem;">Back to all Rooms</a>
                    </div>
                    
                </form>
            </div>
        </div>
    {{-- </div> --}}



@endsection