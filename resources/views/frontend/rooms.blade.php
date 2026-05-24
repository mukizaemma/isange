@extends('layouts.frontbase')

@section('content')

@php
    $activeTab = $activeTab ?? 'rooms';
    $roomsPage = \App\Support\PageContent::get('rooms', $pageHeaders ?? collect());
    $sections = $roomsPage['sections'];

    if ($activeTab === 'apartments') {
        $bannerTitle = $sections['apartments_title'] ?? 'Apartments';
        $bannerSubtitle = $sections['apartments_intro'] ?? '';
    } else {
        $bannerTitle = $sections['rooms_title'] ?? 'Rooms';
        $bannerSubtitle = $sections['rooms_intro'] ?? '';
    }
@endphp

@include('frontend.includes.page-header', [
    'pageKey' => 'rooms',
    'title' => $bannerTitle,
    'subtitle' => $bannerSubtitle,
])

@include('frontend.layouts.rooms')

@endsection
