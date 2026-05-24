@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'terms'])

        <!-- Terms Area start -->
        @include('frontend.layouts.terms')
        <!-- Terms Area end -->
        


@endsection