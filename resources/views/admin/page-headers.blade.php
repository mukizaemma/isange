@extends('layouts.adminbase')

@section('title', 'Page banners')

@section('sidebar')
    @parent
@endsection

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="content">
                <div class="container-fluid py-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                        <div>
                            <h1 class="h3 mb-1">Page banners</h1>
                            <p class="text-muted mb-0">Manage hero images and captions for inner pages. Images are full-width and anchored from the top.</p>
                        </div>
                        @if (session()->has('success'))
                            <div class="alert alert-success mb-0 py-2 px-3">{{ session('success') }}</div>
                        @endif
                    </div>

                    <form action="{{ route('pageHeaders.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            @foreach ($headers as $header)
                                <div class="col-lg-6">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header fw-semibold">{{ $header->label }}</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label" for="title-{{ $header->page_key }}">Heading</label>
                                                <input type="text" class="form-control" id="title-{{ $header->page_key }}" name="headers[{{ $header->page_key }}][title]" value="{{ old('headers.'.$header->page_key.'.title', $header->title) }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="subtitle-{{ $header->page_key }}">Caption / subheading</label>
                                                <textarea class="form-control" id="subtitle-{{ $header->page_key }}" name="headers[{{ $header->page_key }}][subtitle]" rows="3">{{ old('headers.'.$header->page_key.'.subtitle', $header->subtitle) }}</textarea>
                                            </div>
                                            <div>
                                                <label class="form-label">Hero image</label>
                                                @if (! empty($header->hero_image))
                                                    <div class="mb-2 border rounded p-2 bg-light">
                                                        <img src="{{ asset('storage/images/pages/' . $header->hero_image) }}" alt="" class="img-fluid rounded" style="max-height: 140px; object-fit: cover; object-position: top;">
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control" name="headers[{{ $header->page_key }}][hero_image]" accept="image/*">
                                                <div class="form-text">Recommended wide landscape (1600×900+). Top of the image stays visible on all screen sizes.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save page banners</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
