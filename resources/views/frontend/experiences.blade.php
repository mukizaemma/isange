@extends('layouts.frontbase')

@section('content')

@php
    $page = \App\Support\PageContent::get('experiences', $pageHeaders ?? collect());
    $experiences = $page['sections']['items'] ?? [];
@endphp

@include('frontend.includes.page-header', ['pageKey' => 'experiences'])

<section class="isange-section rel z-1 bgc-white">
    <div class="container">
        @if (! empty($page['intro_html']))
        <div class="row justify-content-center mb-50">
            <div class="col-lg-8 text-center wow fadeInUp">
                <div class="welcome-prose">{!! $page['intro_html'] !!}</div>
            </div>
        </div>
        @endif

        <div class="row g-4">
            @foreach ($experiences as $exp)
            <div class="col-md-6 col-lg-4 wow fadeInUp delay-0-2s" id="{{ $exp['id'] ?? '' }}">
                <article class="isange-impact-card h-100">
                    <i class="fas {{ $exp['icon'] ?? 'fa-star' }}" aria-hidden="true"></i>
                    <h3>{{ $exp['title'] ?? '' }}</h3>
                    <p class="mb-3">{{ $exp['text'] ?? '' }}</p>
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
