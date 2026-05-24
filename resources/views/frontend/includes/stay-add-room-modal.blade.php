<div class="modal fade" id="stayAddRoomModal" tabindex="-1" aria-labelledby="stayAddRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stayAddRoomModalLabel">Add room to your stay</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stay-add-room-form">
                <div class="modal-body">
                    <input type="hidden" name="room_id">
                    <input type="hidden" name="room_slug">
                    <input type="hidden" name="room_name">
                    <input type="hidden" name="room_price">
                    <input type="hidden" name="room_image">
                    <p class="small text-muted mb-3" id="stay-add-room-name-label">Select dates and guests for this room.</p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label" for="stay-ar-checkin">Check-in <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="stay-ar-checkin" name="check_in" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="stay-ar-checkout">Check-out <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="stay-ar-checkout" name="check_out" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="stay-ar-adults">Adults</label>
                            <input type="number" class="form-control" id="stay-ar-adults" name="adults" min="1" max="20" value="2">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="stay-ar-children">Children</label>
                            <input type="number" class="form-control" id="stay-ar-children" name="children" min="0" max="20" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="theme-btn">Add to cart</button>
                </div>
            </form>
        </div>
    </div>
</div>
