@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'blogs'])

@include('frontend.layouts.blogs')

@include('frontend.layouts.gallery')
@endsection
