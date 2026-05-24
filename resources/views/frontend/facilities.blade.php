@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'facilities'])

@if (! empty($setting->facilities_intro))
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="lead">{!! $setting->facilities_intro !!}</div>
            </div>
        </div>
    </div>
</section>
@endif

@include('frontend.layouts.facilities')

@include('frontend.layouts.facilities-dining-gallery')

@include('frontend.layouts.gallery')

@endsection
