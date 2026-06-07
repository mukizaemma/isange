{{-- Expects $room (Room model) — image, title, description only --}}
<div class="room-item style-three ma-room-card home-room-card h-100 d-flex flex-column wow fadeInUp delay-0-2s">
    <div class="image home-room-card__image">
        <a href="{{ route('singleRoom', ['slug' => $room->slug]) }}" class="d-block h-100">
            <img class="home-room-card__img" src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="{{ $room->roomName }}" loading="lazy" width="800" height="500">
        </a>
    </div>
    <div class="content flex-grow-1 d-flex flex-column">
        <h3 class="mb-15"><a href="{{ route('singleRoom', ['slug' => $room->slug]) }}">{{ $room->roomName }}</a></h3>
        @if (! empty(trim(strip_tags($room->description ?? ''))))
            <div class="ma-room-card__desc text-muted mb-3 flex-grow-1">
                {!! \Illuminate\Support\Str::limit(strip_tags($room->description), 180) !!}
            </div>
        @endif
        <div class="ma-room-card__actions d-flex flex-wrap gap-2 mt-auto">
            <a class="theme-btn style-three" href="{{ route('singleRoom', ['slug' => $room->slug]) }}">View Details <i class="fal fa-angle-right"></i></a>
            <button type="button" class="theme-btn btn-sm"
                data-add-room
                data-room-id="{{ $room->id }}"
                data-room-slug="{{ $room->slug }}"
                data-room-name="{{ $room->roomName }}"
                data-room-price="{{ $room->price }}"
                data-room-image="{{ asset('storage/images/rooms/' . $room->image) }}">
                Add to stay
            </button>
        </div>
    </div>
</div>
