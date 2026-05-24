{{-- Expects $room (Room model) --}}
<div class="room-item style-three ma-room-card wow fadeInUp delay-0-2s">
    <div class="image">
        <a href="{{ route('singleRoom', ['slug' => $room->slug]) }}">
            <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="{{ $room->roomName }}">
        </a>
    </div>
    <div class="content">
        <div class="price">{!! \App\Support\Currency::formatUsdOnly($room->price) !!} <span class="price-suffix">per night</span></div>
        <h3><a href="{{ route('singleRoom', ['slug' => $room->slug]) }}">{{ $room->roomName }}</a></h3>
        <ul class="ma-room-inclusions list-unstyled small mb-2">
            <li><i class="fas fa-wifi me-1" aria-hidden="true"></i> Free Wi-Fi</li>
            <li><i class="fas fa-seedling me-1" aria-hidden="true"></i> Garden views</li>
        </ul>
        <ul class="blog-meta">
            <li>
                <i class="far fa-drafting-compass"></i>
                <span>Size: {{ $room->size }}</span>
            </li>
            <li>
                <i class="far fa-bath"></i>
                <span>Max adults: {{ $room->maxAdults }}</span>
            </li>
            @if ($room->maxChildren)
                <li>
                    <i class="far fa-bed-alt"></i>
                    <span>Max children: {{ $room->maxChildren }}</span>
                </li>
            @endif
        </ul>
        <div class="ma-room-card__actions d-flex flex-wrap gap-2">
            <a class="theme-btn style-three" href="{{ route('singleRoom', ['slug' => $room->slug]) }}">View Details <i class="fal fa-angle-right"></i></a>
            <a class="theme-btn" href="{{ route('room.booking') }}">Book Now <i class="fal fa-angle-right"></i></a>
        </div>
    </div>
</div>
