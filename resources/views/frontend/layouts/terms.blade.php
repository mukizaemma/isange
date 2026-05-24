@php
    $termsHtml = trim(strip_tags($about->terms ?? '')) !== ''
        ? $about->terms
        : (\App\Support\PageContent::html('terms', 'body_html', $pageHeaders ?? collect())
            ?? \App\Support\PageContentDefaults::bodyHtml('terms'));
@endphp

<section class="isange-section rel z-1 bgc-white pt-80 rpt-60 pb-90 rpb-60">
    <div class="container">
        <div class="row justify-content-center wow fadeInUp">
            <div class="col-lg-10">
                @if (! empty(trim(strip_tags($termsHtml ?? ''))))
                    <div class="welcome-prose terms-page-content">
                        {!! $termsHtml !!}
                    </div>
                @else
                    <p class="text-muted text-center mb-0">
                        Terms and conditions will appear here once added in
                        <strong>Site settings → Terms &amp; conditions</strong>.
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>
