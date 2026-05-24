@extends('layouts.adminbase')

@section('title', 'Dining page & gallery')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">@include('admin.includes.sidenav')</div>
    <div id="layoutSidenav_content">
        <main class="ma-admin-page">
            <div class="container-fluid px-4 py-3">
                <div class="ma-page-head mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h1 class="ma-page-title">Dining page &amp; gallery</h1>
                        <p class="text-muted mb-0 small">Public <strong>/dining</strong> hero &amp; intro, and the dining photo strip on <strong>/facilities</strong>.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('diningMenu.manage') }}" class="btn btn-ma-primary btn-sm">
                            <i class="fas fa-book-open me-1"></i> Menu items
                        </a>
                        <a href="{{ route('diningMenu.categories.manage') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-layer-group me-1"></i> Menu categories
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card ma-card mb-4">
                    <div class="card-header ma-card-head"><strong>Page header</strong> — public dining page</div>
                    <div class="card-body">
                        <form action="{{ route('diningMenu.page') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Hero image</label>
                                    @if (! empty($setting->dining_hero_image))
                                        <div class="mb-2"><img src="{{ asset('storage/images/pages/'.$setting->dining_hero_image) }}" alt="" class="img-fluid rounded" style="max-height:160px;"></div>
                                    @endif
                                    <input type="file" name="dining_hero_image" class="form-control" accept="image/*">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Introduction</label>
                                    <textarea name="dining_intro" id="dining_intro" class="form-control" rows="6">{{ old('dining_intro', $setting->dining_intro ?? '') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-ma-primary"><i class="fas fa-save me-1"></i>Save page</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card ma-card mb-4">
                    <div class="card-header ma-card-head"><strong>Dining gallery</strong> — shown on Facilities page</div>
                    <div class="card-body">
                        <form action="{{ route('diningGallery.store') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end mb-4">
                            @csrf
                            <div class="col-md-4"><label class="form-label">Image</label><input type="file" name="image" class="form-control" required accept="image/*"></div>
                            <div class="col-md-4"><label class="form-label">Caption (optional)</label><input type="text" name="caption" class="form-control"></div>
                            <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Add</button></div>
                        </form>
                        <div class="row g-3">
                            @foreach ($gallery as $g)
                                <div class="col-6 col-md-3">
                                    <div class="border rounded p-2 text-center">
                                        <img src="{{ asset('storage/images/dining-gallery/'.$g->image) }}" class="img-fluid rounded mb-2" alt="">
                                        <div class="small text-muted">{{ $g->caption }}</div>
                                        <form action="{{ route('diningGallery.destroy', $g) }}" method="POST" class="mt-2" onsubmit="return confirm('Remove?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    $('#dining_intro').summernote({ height: 220, toolbar: [
        ['style', ['style']], ['font', ['bold', 'underline']], ['para', ['ul', 'ol']], ['insert', ['link']], ['view', ['fullscreen', 'codeview']]
    ]});
});
</script>
@endsection
