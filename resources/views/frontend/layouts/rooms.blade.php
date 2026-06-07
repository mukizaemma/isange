@php
    $activeTab = $activeTab ?? 'rooms';
    $roomsList = $rooms->where('accommodation_type', \App\Models\Room::TYPE_ROOM);
    $apartmentsList = $rooms->where('accommodation_type', \App\Models\Room::TYPE_APARTMENT);
    $displayRooms = $activeTab === 'apartments' ? $apartmentsList : $roomsList;
@endphp
<section class="rooms-2columns-area rooms-on-white pb-30 rpb-90 rel z-2">
    <div class="container container-1130">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <ul class="nav nav-pills justify-content-center ma-accommodation-tabs mb-50 rmb-40 wow fadeInUp delay-0-2s" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $activeTab === 'rooms' ? 'active' : '' }}" href="{{ route('rooms', ['tab' => 'rooms']) }}" role="tab" @if($activeTab === 'rooms') aria-current="page" @endif>Rooms</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $activeTab === 'apartments' ? 'active' : '' }}" href="{{ route('rooms', ['tab' => 'apartments']) }}" role="tab" @if($activeTab === 'apartments') aria-current="page" @endif>Apartments</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row g-4">
            @forelse($displayRooms as $room)
                <div class="col-md-6">
                    @include('frontend.includes.room-card', ['room' => $room])
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <p class="mb-0">No {{ $activeTab === 'apartments' ? 'apartments' : 'rooms' }} listed yet. Please check back soon or <a href="{{ route('contact') }}">contact us</a>.</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="bg-lines for-bg-white">
       <span></span><span></span>
       <span></span><span></span>
       <span></span><span></span>
       <span></span><span></span>
       <span></span><span></span>
    </div>
</section>
