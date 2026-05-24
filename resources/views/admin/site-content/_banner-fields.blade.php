@php
    $key = $pageKey;
    $page = $pageData;
    $row = $headers[$key] ?? null;
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label" for="title-{{ $key }}">Page heading (banner)</label>
        <input type="text" class="form-control" id="title-{{ $key }}" name="pages[{{ $key }}][title]" value="{{ old("pages.{$key}.title", $page['title']) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="subtitle-{{ $key }}">Banner caption</label>
        <textarea class="form-control" id="subtitle-{{ $key }}" name="pages[{{ $key }}][subtitle]" rows="2">{{ old("pages.{$key}.subtitle", $page['subtitle']) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="hero-{{ $key }}">Banner image</label>
        @if (! empty($row?->hero_image))
            <div class="mb-2">
                <img src="{{ asset('storage/images/pages/' . $row->hero_image) }}" alt="" class="img-fluid rounded" style="max-height:120px;">
            </div>
        @endif
        <input type="file" class="form-control" id="hero-{{ $key }}" name="pages[{{ $key }}][hero_image]" accept="image/*">
    </div>
</div>
