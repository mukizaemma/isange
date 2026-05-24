<div class="container">
    <h2>Check the Availability</h2>
    <div class="row" style="background-color:teal; color:#fff; border-radius:7px; padding:12px;">

        <form method="GET" action="{{ route('checkAvailability') }}">

            <div class="row">
                     <div class="col-lg-3">
                      <div class="form-group">
                        <label for="item">Please select service:</label><br>
                        <div class="form-check form-check-inline" style="border-radius:5px;">
                            <input class="form-check-input" type="radio" name="item" id="room" value="Room" required >
                            <label class="form-check-label" for="room">Room</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="item" id="table" value="Table">
                            <label class="form-check-label" for="table">Table or Paddle</label>
                        </div>
                        {{-- <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="item" id="paddle" value="Paddle">
                            <label class="form-check-label" for="paddle">Paddle</label>
                        </div> --}}
                    </div>
                    </div>
                    <div class="col-lg-3">
                        <label for="checkin">Check In Date & Time</label>
                        <input type="datetime-local" id="checkin" class="form-control" name="checkin" required="" style="border-radius:5px;">
                      </div>
                      <div class="col-lg-3">
                        <label for="checkout">Check Out Date & Time</label>
                        <input type="datetime-local" id="checkout" class="form-control" name="checkout" required="" style="border-radius:5px;">
                      </div>

                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary mt-3" style="border-radius:5px;">Check Availability</button>
                    </div>
            </div>
        </form>

  </div>

  </div>