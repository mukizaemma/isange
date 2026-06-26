@php
    $waDigits = preg_replace('/\D+/', '', $setting->phone ?? '');
@endphp

<div id="dining-order-dock" class="dining-order-dock d-none" aria-live="polite">
    <div class="dining-order-dock__inner dining-order-dock__inner--wide py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <strong class="dining-order-dock__label d-block mb-0">Your order</strong>
                <span id="dining-order-count" class="dining-order-dock__sub small">0 items</span>
                <span id="dining-order-prep-estimate" class="dining-order-dock__sub small d-block text-warning"></span>
                <p class="dining-order-dock__pay-note small mb-0 mt-1"><i class="fas fa-info-circle me-1" aria-hidden="true"></i>Pay at the hotel — online payment is not available for restaurant orders.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-end">
                <div>
                    <label class="form-label small mb-1" for="dining-global-time">Time needed</label>
                    <input type="time" class="form-control form-control-sm" id="dining-global-time" style="max-width:9rem">
                </div>
                <div>
                    <label class="form-label small mb-1" for="dining-global-party">Party size</label>
                    <input type="number" class="form-control form-control-sm" id="dining-global-party" min="1" value="2" placeholder="Guests" style="max-width:7rem">
                </div>
            </div>
        </div>
        <div class="row g-3 align-items-start">
            <div class="col-lg-8">
                <div class="dining-order-summary-card rounded-3 border overflow-hidden bg-white text-dark shadow-sm">
                    <div class="dining-order-summary-card__head px-3 py-2 border-bottom">
                        <span class="dining-order-summary-card__title fw-semibold">Order summary</span>
                        <span class="text-muted small ms-2">Review before sending</span>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive dining-order-summary-table-wrap">
                            <table class="table table-sm table-striped align-middle mb-0 dining-order-summary-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col" class="text-end text-nowrap" style="width:4rem;">Qty</th>
                                        <th scope="col" class="text-end text-nowrap" style="width:7rem;">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="dining-order-table-body"></tbody>
                                <tfoot class="table-group-divider" id="dining-order-table-foot"></tfoot>
                            </table>
                        </div>
                        <h3 class="h6 fw-semibold mt-4 mb-2">Your details</h3>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small mb-1" for="dining-guest-name">Name</label>
                                <input type="text" class="form-control form-control-sm" id="dining-guest-name" maxlength="255" autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1" for="dining-guest-phone">Phone / WhatsApp</label>
                                <input type="tel" class="form-control form-control-sm" id="dining-guest-phone" maxlength="64" autocomplete="tel">
                            </div>
                            <div class="col-12">
                                <label class="form-label small mb-1" for="dining-guest-email">Email</label>
                                <input type="email" class="form-control form-control-sm" id="dining-guest-email" maxlength="255" autocomplete="email">
                            </div>
                        </div>
                        <label class="form-label small fw-semibold mt-3 mb-1" for="dining-order-additional">Special requests (optional)</label>
                        <textarea class="form-control form-control-sm dining-order-summary-card__textarea" id="dining-order-additional" rows="2" placeholder="Allergies, room number, delivery preference, occasion…"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <p class="dining-order-dock__sub small mb-2">Send your order to the hotel. Payment is collected on site.</p>
                <x-human-form-fields class="mb-3" />
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="theme-btn btn-sm" id="dining-order-whatsapp"><i class="fab fa-whatsapp me-1"></i> Send via WhatsApp</button>
                    <button type="button" class="theme-btn style-three btn-sm" id="dining-order-email"><i class="far fa-envelope me-1"></i> Send via Email</button>
                    <button type="button" class="btn btn-outline-light btn-sm" id="dining-order-clear">Clear order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dining-add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dining-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="dining-modal-dish-name">Add to order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <input type="hidden" id="dining-add-id">
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="dining-add-qty" min="1" value="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Special request (optional)</label>
                    <textarea class="form-control" id="dining-add-notes" rows="2" placeholder="No onions, extra sauce, allergies…"></textarea>
                </div>
                <button type="button" class="theme-btn w-100 mt-2" id="dining-add-confirm">Add to tray</button>
            </div>
        </div>
    </div>
</div>
