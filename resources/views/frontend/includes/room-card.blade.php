{{-- Expects $room (Room model) — shared listing card for rooms page --}}
<article class="room-item style-three ma-room-card home-room-card h-100 d-flex flex-column wow fadeInUp delay-0-2s">
    <div class="image home-room-card__image">
        <a href="{{ route('singleRoom', ['slug' => $room->slug]) }}" class="d-block h-100" tabindex="-1" aria-hidden="true">
            <img class="home-room-card__img" src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="" loading="lazy" width="800" height="500">
        </a>
    </div>
    <div class="content flex-grow-1 d-flex flex-column">
        <h3 class="home-room-card__title mb-2">
            <a href="{{ route('singleRoom', ['slug' => $room->slug]) }}">{{ $room->roomName }}</a>
        </h3>
        @include('frontend.includes.room-price', ['room' => $room])
        @if (! empty(trim(strip_tags($room->description ?? ''))))
            <p class="home-room-card__desc text-muted mb-3 flex-grow-1">
                {!! \Illuminate\Support\Str::limit(strip_tags($room->description), 160) !!}
            </p>
        @else
            <div class="flex-grow-1"></div>
        @endif
        <div class="home-room-card__actions ma-room-card__actions mt-auto">
            <button type="button"
                class="theme-btn home-room-card__btn home-room-card__btn--book"
                data-add-room
                data-room-id="{{ $room->id }}"
                data-room-slug="{{ $room->slug }}"
                data-room-name="{{ $room->roomName }}"
                data-room-price="{{ $room->bookingPriceUsd((bool) auth()->user()?->hasUnlockedDiscount()) }}"
                data-room-image="{{ asset('storage/images/rooms/' . $room->image) }}">
                <span data-add-room-label>Book Now</span>
            </button>
            <a class="home-room-card__details" href="{{ route('singleRoom', ['slug' => $room->slug]) }}">
                View details <i class="far fa-angle-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</article>
