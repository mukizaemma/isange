{{-- Accommodation: Rooms and Apartments link to listing pages (no per-item flyout) --}}
<li class="dropdown">
    <a href="{{ route('rooms') }}">Accommodation</a>
    <ul>
        <li>
            <a href="{{ route('rooms', ['tab' => 'rooms']) }}">Rooms</a>
        </li>
        <li>
            <a href="{{ route('rooms', ['tab' => 'apartments']) }}">Apartments</a>
        </li>
    </ul>
</li>
