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
                @include('frontend.includes.blog-update-cards', ['blogs' => $blogs])
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
