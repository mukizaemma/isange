@php
    $aboutMediaSize = \App\Support\PageHeaderResolver::dimensions($src ?? null);
@endphp
<figure @class([
    'isange-about-media mb-0',
    $class ?? '',
    'has-ratio' => (bool) $aboutMediaSize,
])
    @if ($aboutMediaSize)
        style="--img-w: {{ $aboutMediaSize['width'] }}; --img-h: {{ $aboutMediaSize['height'] }};"
    @endif
>
    <img
        src="{{ $src }}"
        alt="{{ $alt ?? 'Isange Paradise Eco Resort' }}"
        loading="lazy"
        @if ($aboutMediaSize)
            width="{{ $aboutMediaSize['width'] }}"
            height="{{ $aboutMediaSize['height'] }}"
        @endif
    >
</figure>
@once
    @push('scripts')
        <script>
            (function () {
                function fitAboutMedia(img) {
                    var figure = img.closest('.isange-about-media');
                    if (!figure || !img.naturalWidth || !img.naturalHeight) {
                        return;
                    }
                    figure.style.setProperty('--img-w', String(img.naturalWidth));
                    figure.style.setProperty('--img-h', String(img.naturalHeight));
                    figure.classList.add('has-ratio');
                }

                document.querySelectorAll('.isange-about-media img').forEach(function (img) {
                    if (img.complete) {
                        fitAboutMedia(img);
                    } else {
                        img.addEventListener('load', function () {
                            fitAboutMedia(img);
                        });
                    }
                });
            })();
        </script>
    @endpush
@endonce
