@extends('layouts.frontbase')

@section('content')

@php
    $page = \App\Support\PageContent::get('future4kids', $pageHeaders ?? collect());
    $s = $page['sections'];
    $bullets = $s['mission_bullets'] ?? [];
    $bulletIcons = ['fa-graduation-cap', 'fa-heartbeat', 'fa-tools', 'fa-female', 'fa-users'];
@endphp

@include('frontend.includes.page-header', ['pageKey' => 'future4kids'])

<section class="isange-section isange-section--cream rel z-1">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7 wow fadeInUp delay-0-2s">
                @if (! empty($s['mission_eyebrow']))
                    <span class="isange-section__eyebrow">{{ $s['mission_eyebrow'] }}</span>
                @endif
                @if (! empty($s['mission_title']))
                    <h2>{{ $s['mission_title'] }}</h2>
                @endif
                @if (! empty($s['mission_lead']))
                    <p class="lead mb-4">{{ $s['mission_lead'] }}</p>
                @endif
                @if (! empty($s['mission_text']))
                    <p>{!! $s['mission_text'] !!}</p>
                @endif
                @if (count($bullets) > 0)
                <ul class="isange-purpose-list isange-purpose-list--compact">
                    @foreach ($bullets as $i => $bullet)
                        <li><i class="fas {{ $bulletIcons[$i] ?? 'fa-check' }}" aria-hidden="true"></i> {{ $bullet }}</li>
                    @endforeach
                </ul>
                @endif
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="https://www.future4kids.at/" class="theme-btn" target="_blank" rel="noopener noreferrer">
                        Visit Future 4 Kids website <i class="fas fa-external-link-alt"></i>
                    </a>
                    <a href="{{ route('room.booking') }}" class="theme-btn style-three">Book and support our mission <i class="far fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-5 wow fadeInUp delay-0-3s">
                <div class="isange-impact-card isange-impact-card--clean p-4 p-md-5">
                    <h3>{{ $s['impact_title'] ?? 'Travel that gives back' }}</h3>
                    <p class="mb-0">{{ $s['impact_text'] ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="isange-section rel z-1 bgc-white" id="shop">
    <div class="container">
        <div class="row justify-content-center text-center mb-45 wow fadeInUp">
            <div class="col-lg-7">
                @if (! empty($s['shop_eyebrow']))
                    <span class="isange-section__eyebrow">{{ $s['shop_eyebrow'] }}</span>
                @endif
                <h2>{{ $s['shop_title'] ?? 'Future 4 Kids Shop' }}</h2>
                <p class="mb-0">{{ $s['shop_intro'] ?? '' }}</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ($s['shop_items'] ?? [] as $item)
            <div class="col-md-6 col-lg-3 wow fadeInUp delay-0-2s">
                <div class="isange-why-card isange-why-card--minimal h-100">
                    <div class="isange-why-card__icon"><i class="fas {{ $item['icon'] ?? 'fa-store' }}" aria-hidden="true"></i></div>
                    <h3 class="h6">{{ $item['title'] ?? '' }}</h3>
                    <p class="mb-0 small text-muted">{{ $item['text'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-50">
            <p class="text-muted mb-3">{{ $s['shop_footer'] ?? 'Visit the shop during your stay, or contact us for availability.' }}</p>
            <a href="{{ route('contact') }}" class="theme-btn style-three btn-sm">Contact us <i class="far fa-angle-right"></i></a>
        </div>
    </div>
</section>

@include('frontend.includes.youtube-stories-widget', ['variant' => 'cream'])

@endsection
