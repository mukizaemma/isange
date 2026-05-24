@extends('layouts.frontbase')

@section('content')

@php
    $otaTitles = [
        'expedia' => 'Continue on Expedia',
        'emerging_travel' => 'Continue on Emerging Travel Group',
        'booking_com' => 'Continue on Booking.com',
    ];
@endphp

@include('frontend.includes.page-header', [
    'pageKey' => 'booking',
    'title' => $otaTitles[$which] ?? 'Continue booking',
    'subtitle' => 'Your request is saved. Complete your reservation on the partner site in the new tab.',
])

<section class="py-100 rpy-70 bg-white rel z-1">
    <div class="container text-center">
        <p class="mb-4">If the new tab was blocked, use the button below.</p>
        <a class="theme-btn" href="{{ $url }}" target="_blank" rel="noopener noreferrer">Open partner site</a>
        <script>
            (function () {
                var u = @json($url);
                window.open(u, '_blank', 'noopener,noreferrer');
            })();
        </script>
    </div>
</section>
@endsection
