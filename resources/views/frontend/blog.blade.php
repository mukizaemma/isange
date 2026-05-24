@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => $post->title,
    'subtitle' => optional($post->published_at)->format('d M Y'),
    'imageUrl' => null,
])

<section class="blog-details-area py-100 rpy-80 rel z-1">
    <div class="container container-1130">
        <article class="blog-details-content">
            <div class="welcome-prose">
                {!! $post->body !!}
            </div>
            <p class="mt-40">
                <a href="{{ route('blogs') }}" class="theme-btn style-three">Back to updates <i class="far fa-angle-right"></i></a>
            </p>
        </article>
    </div>
</section>

@endsection
