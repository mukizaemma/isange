@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Open your email app',
    'subtitle' => 'Tap the button below to send your saved booking request to the hotel.',
    'imageUrl' => null,
])

<section class="py-100 rpy-70 bg-white rel z-1">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @php
                    $mailHref = 'mailto:' . $email . '?subject=' . rawurlencode($subject) . '&body=' . rawurlencode($booking->message_body);
                @endphp
                <a class="theme-btn btn-lg" href="{{ $mailHref }}"><i class="far fa-envelope me-2"></i> Send with {{ $email }}</a>
                <p class="small text-muted mt-4 mb-0">If nothing opens, copy the hotel email from the contact page and paste this request manually.</p>
            </div>
        </div>
    </div>
</section>
@endsection
