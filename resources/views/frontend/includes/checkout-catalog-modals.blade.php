{{-- Pick room / experience without leaving checkout --}}
<div class="modal fade ma-checkout-pick-modal" id="checkoutPickRoomModal" tabindex="-1" aria-labelledby="checkoutPickRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg ma-checkout-pick-modal__dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="checkoutPickRoomModalLabel">Add a room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                @if ($rooms->isNotEmpty())
                    <div class="ma-checkout-pick-list">
                        @foreach ($rooms as $r)
                            <article class="ma-checkout-pick-item" data-checkout-room-id="{{ $r->id }}">
                                @if ($r->image)
                                    <img src="{{ asset('storage/images/rooms/'.$r->image) }}" alt="" class="ma-checkout-pick-item__img" loading="lazy">
                                @else
                                    <span class="ma-checkout-pick-item__img ma-checkout-pick-item__img--placeholder"><i class="fas fa-bed"></i></span>
                                @endif
                                <div class="ma-checkout-pick-item__body">
                                    <h6 class="ma-checkout-pick-item__title">{{ $r->roomName }}</h6>
                                    @if ($r->listPriceUsd() !== null)
                                        @if (($discountUnlocked ?? false) && $r->hasActiveDiscount())
                                            <p class="ma-checkout-pick-item__meta small text-muted mb-0">
                                                From <span class="text-decoration-line-through">{{ \App\Support\Currency::formatRoomPriceLabel($r->listPriceUsd()) }}</span>
                                                {!! \App\Support\Currency::formatUsdWithLocal($r->salePriceUsd(), $r->salePriceRwf()) !!} / night
                                                <span class="badge bg-success">{{ $r->discountBadgeLabel() }}</span>
                                            </p>
                                        @else
                                            <p class="ma-checkout-pick-item__meta small text-muted mb-0">From {!! \App\Support\Currency::formatUsdWithLocal($r->bookingPriceUsd(false), $r->bookingPriceRwf(false)) !!} / night</p>
                                        @endif
                                    @else
                                        <p class="ma-checkout-pick-item__meta small text-muted mb-0">Rate on request</p>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm theme-btn ma-checkout-pick-item__add"
                                    data-checkout-add-room
                                    data-room-id="{{ $r->id }}"
                                    data-room-slug="{{ $r->slug }}"
                                    data-room-name="{{ $r->roomName }}"
                                    data-room-price="{{ $r->bookingPriceUsd((bool) ($discountUnlocked ?? false)) }}"
                                    data-room-image="{{ $r->image ? asset('storage/images/rooms/'.$r->image) : '' }}">
                                    Add
                                </button>
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No rooms available. <a href="{{ route('rooms') }}">Browse accommodation</a>.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade ma-checkout-pick-modal" id="checkoutPickExperienceModal" tabindex="-1" aria-labelledby="checkoutPickExperienceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg ma-checkout-pick-modal__dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="checkoutPickExperienceModalLabel">Add an experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                @php $expItems = collect($experiences ?? []); @endphp
                @if ($expItems->isNotEmpty())
                    <div class="ma-checkout-pick-list ma-checkout-pick-list--grid">
                        @foreach ($expItems as $exp)
                            @php
                                $expId = $exp['id'] ?? '';
                                $expTitle = $exp['title'] ?? $expId;
                                $expIcon = $exp['icon'] ?? 'fa-star';
                            @endphp
                            <article class="ma-checkout-pick-item" data-checkout-exp-id="{{ $expId }}">
                                <span class="ma-checkout-pick-item__img ma-checkout-pick-item__img--placeholder"><i class="fas {{ $expIcon }}"></i></span>
                                <div class="ma-checkout-pick-item__body">
                                    <h6 class="ma-checkout-pick-item__title">{{ $expTitle }}</h6>
                                    @if (! empty($exp['text']))
                                        <p class="ma-checkout-pick-item__meta small text-muted mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($exp['text']), 80) }}</p>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm theme-btn ma-checkout-pick-item__add"
                                    data-checkout-add-experience
                                    data-exp-id="{{ $expId }}"
                                    data-exp-title="{{ $expTitle }}"
                                    data-exp-icon="{{ $expIcon }}">
                                    Add
                                </button>
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No experiences listed. <a href="{{ route('experiences') }}">View experiences</a>.</p>
                @endif
            </div>
        </div>
    </div>
</div>
