@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'experiences'])

<section class="isange-section rel z-1 bgc-white">
    <div class="container">
        <div class="row justify-content-center mb-50">
            <div class="col-lg-8 text-center wow fadeInUp">
                <p>Isange Paradise is your base for unforgettable adventures near Volcanoes National Park. We can help arrange your full itinerary — tell us your dates and interests when you book.</p>
            </div>
        </div>

        @php
            $experiences = [
                ['id' => 'gorilla', 'icon' => 'fa-paw', 'title' => 'Gorilla Trekking', 'text' => 'Track mountain gorillas in Volcanoes National Park — one of Africa’s most profound wildlife experiences, just 15 minutes from the resort.'],
                ['id' => 'golden-monkey', 'icon' => 'fa-tree', 'title' => 'Golden Monkey Trekking', 'text' => 'Meet playful golden monkeys in their bamboo forest habitat — a lighter trek ideal for families and nature lovers.'],
                ['id' => 'volcano', 'icon' => 'fa-mountain', 'title' => 'Volcano Hiking', 'text' => 'Hike the Virunga volcanoes for panoramic views of Rwanda, Uganda, and the DRC — guided options for varied fitness levels.'],
                ['id' => 'caves', 'icon' => 'fa-dungeon', 'title' => 'Musanze Caves', 'text' => 'Explore ancient lava tubes beneath Musanze with expert guides — geology, history, and adventure underground.'],
                ['id' => 'lakes', 'icon' => 'fa-water', 'title' => 'Twin Lakes', 'text' => 'Visit Burera and Ruhondo — twin crater lakes surrounded by hills, perfect for boat trips, picnics, and photography.'],
                ['id' => 'culture', 'icon' => 'fa-people-arrows', 'title' => 'Cultural Village Experiences', 'text' => 'Connect with local traditions, dance, crafts, and daily life in communities around Musanze.'],
                ['id' => 'birds', 'icon' => 'fa-dove', 'title' => 'Bird Watching', 'text' => 'Discover Albertine Rift endemics and forest species in gardens, wetlands, and park buffer zones.'],
                ['id' => 'cycling', 'icon' => 'fa-bicycle', 'title' => 'Cycling Tours', 'text' => 'Pedal scenic routes through villages and farmland with views of the Virungas.'],
                ['id' => 'community', 'icon' => 'fa-hands-helping', 'title' => 'Community Visits', 'text' => 'See how Future 4 Kids programs support education, health, and empowerment — travel with purpose.'],
            ];
        @endphp

        <div class="row g-4">
            @foreach ($experiences as $exp)
            <div class="col-md-6 col-lg-4 wow fadeInUp delay-0-2s" id="{{ $exp['id'] }}">
                <article class="isange-impact-card h-100">
                    <i class="fas {{ $exp['icon'] }}" aria-hidden="true"></i>
                    <h3>{{ $exp['title'] }}</h3>
                    <p class="mb-3">{{ $exp['text'] }}</p>
                    <a href="{{ route('contact') }}" class="theme-btn style-three btn-sm">Plan this experience <i class="far fa-angle-right"></i></a>
                </article>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-60 wow fadeInUp">
            <a href="{{ route('room.booking') }}" class="theme-btn">Book your stay &amp; adventures <i class="far fa-angle-right"></i></a>
        </div>
    </div>
</section>

@include('frontend.includes.youtube-stories-widget', ['variant' => 'cream'])

@endsection
