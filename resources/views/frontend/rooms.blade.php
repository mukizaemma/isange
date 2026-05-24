@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'pageKey' => 'rooms',
    'highlights' => [
        [
            'title' => $setting->flexible_stay_card1_title ?: 'Garden Views',
            'text' => $setting->flexible_stay_card1_text ?: 'Peaceful rooms with balconies, terraces, and lush surroundings.',
        ],
        [
            'title' => $setting->flexible_stay_card2_title ?: 'Eco Comfort',
            'text' => $setting->flexible_stay_card2_text ?: 'Private bathrooms, hot showers, Wi-Fi, and quality bedding.',
        ],
        [
            'title' => $setting->flexible_stay_card3_title ?: 'For Every Traveler',
            'text' => $setting->flexible_stay_card3_text ?: 'From solo explorers and couples to families and adventure tents.',
        ],
    ],
])

@include('frontend.layouts.rooms')

@endsection
