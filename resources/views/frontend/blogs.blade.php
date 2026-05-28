@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'blogs'])

<section class="isange-updates isange-section isange-section--cream rel z-1">
    <div class="container">
        @if ($blogs->isEmpty())
            <div class="isange-updates__empty text-center py-5">
                <i class="far fa-newspaper fa-3x text-muted mb-3" aria-hidden="true"></i>
                <p class="lead mb-0">News and updates will appear here soon. Check back shortly.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach ($blogs as $blog)
                    <div class="col-md-6 col-lg-4 wow fadeInUp delay-0-2s">
                        <article class="isange-update-card h-100">
                            <a href="{{ route('blog', $blog->slug) }}" class="isange-update-card__image-link">
                                <img src="{{ $blog->imageUrl() }}" alt="{{ $blog->title }}" class="isange-update-card__image" loading="lazy">
                            </a>
                            <div class="isange-update-card__body">
                                <div class="isange-update-card__meta">
                                    <span><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ optional($blog->published_at)->format('d M Y') }}</span>
                                    <span><i class="far fa-eye" aria-hidden="true"></i> {{ number_format($blog->views ?? 0) }}</span>
                                </div>
                                <h3 class="isange-update-card__title">
                                    <a href="{{ route('blog', $blog->slug) }}">{{ $blog->title }}</a>
                                </h3>
                                <p class="isange-update-card__excerpt">{{ $blog->excerpt() }}</p>
                                <a href="{{ route('blog', $blog->slug) }}" class="isange-update-card__more">
                                    Read more <i class="fal fa-angle-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
            @if ($blogs->hasPages())
                <div class="isange-updates__pagination mt-5 d-flex justify-content-center">
                    {{ $blogs->links() }}
                </div>
            @endif
        @endif
    </div>
</section>

@endsection
