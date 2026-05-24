@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'services'])

    @include('frontend.layouts.services')

    @include('frontend.layouts.gallery')

@endsection
