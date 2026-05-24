{{-- Expects $room (Room model) — image, title, description only --}}
<div class="room-item style-three ma-room-card wow fadeInUp delay-0-2s">
    <div class="image">
        <a href="{{ route('singleRoom', ['slug' => $room->slug]) }}">
            <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="{{ $room->roomName }}">
        </a>
    </div>
    <div class="content">
        <h3><a href="{{ route('singleRoom', ['slug' => $room->slug]) }}">{{ $room->roomName }}</a></h3>
        @if (! empty(trim(strip_tags($room->description ?? ''))))
            <div class="ma-room-card__desc text-muted mb-3">
                {!! \Illuminate\Support\Str::limit(strip_tags($room->description), 180) !!}
            </div>
        @endif
        <div class="ma-room-card__actions">
            <a class="theme-btn style-three" href="{{ route('singleRoom', ['slug' => $room->slug]) }}">View Details <i class="fal fa-angle-right"></i></a>
        </div>
    </div>
</div>
