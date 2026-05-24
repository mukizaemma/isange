@extends('layouts.frontbase')

@section('content')

@php
    use App\Support\PageHeaderResolver;
    $aboutPage = \App\Support\PageContent::get('about', $pageHeaders ?? collect());
    $as = $aboutPage['sections'];
    $aboutHeaderRow = ($pageHeaders ?? collect())['about'] ?? null;
    $aboutSideImage = PageHeaderResolver::resolve('about', $setting, $about, $aboutHeaderRow)['imageUrl']
        ?? (! empty($about->aboutImage)
            ? asset('storage/images/gallery/' . ltrim($about->aboutImage, '/'))
            : null);
@endphp

@include('frontend.includes.page-header', ['pageKey' => 'about'])

<section class="who-we-are-area pb-130 rpb-100 rel z-1 isange-section--cream">
    <div class="container">
        <div class="row justify-content-between align-items-center g-4">
            <div class="col-xl-6 col-lg-7">
                <div class="who-we-are-content wow fadeInUp delay-0-2s">
                    @if (! empty($as['story_eyebrow']))
                        <span class="isange-section__eyebrow">{{ $as['story_eyebrow'] }}</span>
                    @endif
                    <div class="section-title mb-35">
                        <h2>{{ $as['story_title'] ?? 'Welcome to Isange Paradise' }}</h2>
                        @if (! empty($aboutPage['intro_html']))
                            <div class="welcome-prose">{!! $aboutPage['intro_html'] !!}</div>
                        @endif
                        @if (! empty($about->welcome))
                            <div class="welcome-prose mt-3">{!! $about->welcome !!}</div>
                        @endif
                    </div>
                    <a class="theme-btn" href="{{ route('future4kids') }}">Our impact mission <i class="far fa-angle-right"></i></a>
                </div>
            </div>
            @if (! empty($aboutSideImage))
            <div class="col-lg-5">
                <div class="isange-about-media wow fadeInUp delay-0-4s">
                    <img src="{{ $aboutSideImage }}" alt="Isange Paradise Eco Resort" loading="lazy">
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<section class="isange-section rel z-1 bgc-white" id="team">
    <div class="container">
        <div class="row justify-content-center text-center mb-45">
            <div class="col-lg-8 wow fadeInUp">
                @if (! empty($as['team_eyebrow']))
                    <span class="isange-section__eyebrow">{{ $as['team_eyebrow'] }}</span>
                @endif
                <h2>{{ $as['team_title'] ?? 'Warm Rwandan Hospitality' }}</h2>
                <p class="mb-0">{{ $as['team_intro'] ?? '' }}</p>
            </div>
        </div>
        @if (! empty($about->chooseUs))
            <div class="row justify-content-center wow fadeInUp">
                <div class="col-lg-10 welcome-prose">{!! $about->chooseUs !!}</div>
            </div>
        @endif
    </div>
</section>

@include('frontend.includes.youtube-stories-widget', ['variant' => 'white'])

@endsection
