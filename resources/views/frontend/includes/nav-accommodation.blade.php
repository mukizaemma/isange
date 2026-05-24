{{-- Accommodation mega-menu: Rooms and Apartments with listing links --}}
@php
    $navRoomsList = $navRoomsList ?? collect();
    $navApartmentsList = $navApartmentsList ?? collect();
@endphp

<li class="dropdown">
    <a href="{{ route('rooms') }}">Accommodation</a>
    <ul>
        <li class="dropdown">
            <a href="{{ route('rooms', ['tab' => 'rooms']) }}">Rooms</a>
            <ul>
                @forelse ($navRoomsList as $navRoom)
                    <li>
                        <a href="{{ route('singleRoom', ['slug' => $navRoom->slug]) }}">{{ $navRoom->roomName }}</a>
                    </li>
                @empty
                    <li>
                        <a href="{{ route('rooms', ['tab' => 'rooms']) }}">View all rooms</a>
                    </li>
                @endforelse
            </ul>
        </li>
        <li class="dropdown">
            <a href="{{ route('rooms', ['tab' => 'apartments']) }}">Apartments</a>
            <ul>
                @forelse ($navApartmentsList as $navApartment)
                    <li>
                        <a href="{{ route('singleRoom', ['slug' => $navApartment->slug]) }}">{{ $navApartment->roomName }}</a>
                    </li>
                @empty
                    <li>
                        <a href="{{ route('rooms', ['tab' => 'apartments']) }}">View all apartments</a>
                    </li>
                @endforelse
            </ul>
        </li>
    </ul>
</li>
