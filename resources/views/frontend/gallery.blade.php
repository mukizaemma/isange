@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'pageKey' => 'gallery',
    'imageUrl' => isset($images) && $images->count()
        ? asset('storage/images/gallery/'.$images->first()->image)
        : null,
])

        <!-- Gallery Area start -->
        @include('frontend.includes.gallery')
        
        <!-- Gallery Area end -->



@endsection
