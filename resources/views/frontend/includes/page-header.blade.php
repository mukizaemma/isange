{{--
    Shared inner-page header: optional hero background, otherwise solid bar.
    Pass pageKey for admin-managed banner, or title/subtitle/imageUrl directly.
--}}
@php
    use App\Support\PageHeaderResolver;

    $__pageKey = $pageKey ?? null;
    $__highlights = $highlights ?? [];

    if ($__pageKey) {
        $__headerRow = ($pageHeaders ?? collect())[$__pageKey] ?? null;
        $__resolved = PageHeaderResolver::resolve(
            $__pageKey,
            $setting ?? null,
            $about ?? null,
            $__headerRow,
            array_filter([
                'title' => $title ?? null,
                'subtitle' => $subtitle ?? null,
                'imageFile' => $imageFile ?? null,
            ])
        );
        $__title = $__resolved['title'];
        $__subtitle = $__resolved['subtitle'];
        $__img = ! empty($imageUrl ?? null) ? $imageUrl : $__resolved['imageUrl'];
    } else {
        $__title = $title ?? ($pageTitle ?? 'Page');
        $__subtitle = $subtitle ?? null;
        $__img = $imageUrl ?? null;
    }

    $__fullHeight = $fullHeight ?? ($__img !== null);
    $__bannerClass = 'page-banner-area page-banner-area--fullbleed parallax-bg rel z-1 bgc-black text-center';
    if ($__img) {
        $__bannerClass .= $__fullHeight ? ' page-banner-area--fullscreen' : ' page-banner-area--banner';
    }
@endphp
@if ($__img)
<section class="{{ $__bannerClass }} bgs-cover" style="background-image: url('{{ $__img }}');">
    <div class="page-banner-area__overlay" aria-hidden="true"></div>
    <div class="container page-banner-area__content">
        <div class="banner-inner text-white">
            <h1 class="page-title wow fadeInUp delay-0-2s">{{ $__title }}</h1>
            @if ($__subtitle)
                <p class="mb-0 wow fadeInUp delay-0-3s page-banner-area__subtitle">{{ $__subtitle }}</p>
            @endif
            @if (! empty($__highlights))
                <div class="row g-3 justify-content-center mt-4">
                    @foreach ($__highlights as $item)
                        <div class="col-lg-4 col-md-6">
                            <div class="px-3 py-3 rounded h-100 page-banner-area__highlight">
                                <h5 class="text-white mb-2">{{ $item['title'] ?? '' }}</h5>
                                <p class="mb-0 page-banner-area__highlight-text">{{ $item['text'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="bg-lines">
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
    </div>
</section>
@else
<section class="page-banner-area page-banner-area--solid pt-100 rpt-70 pb-100 rpb-70 rel z-1 bgc-black text-center text-white">
    <div class="container py-3">
        <div class="banner-inner text-white">
            <h1 class="page-title wow fadeInUp delay-0-2s">{{ $__title }}</h1>
            @if ($__subtitle)
                <p class="mb-0 wow fadeInUp delay-0-3s page-banner-area__subtitle">{{ $__subtitle }}</p>
            @endif
            @if (! empty($__highlights))
                <div class="row g-3 justify-content-center mt-4">
                    @foreach ($__highlights as $item)
                        <div class="col-lg-4 col-md-6">
                            <div class="px-3 py-3 rounded h-100 page-banner-area__highlight page-banner-area__highlight--solid">
                                <h5 class="text-white mb-2">{{ $item['title'] ?? '' }}</h5>
                                <p class="mb-0 page-banner-area__highlight-text">{{ $item['text'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="bg-lines">
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
    </div>
</section>
@endif
