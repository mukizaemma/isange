@if (isset($diningGallery) && $diningGallery->count())
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2>Dining &amp; atmosphere</h2>
            <p class="text-muted">A glimpse of our restaurant and spaces</p>
        </div>
        <div class="row g-3">
            @foreach ($diningGallery as $photo)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
                        <img src="{{ asset('storage/images/dining-gallery/'.$photo->image) }}" alt="{{ $photo->caption }}" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    @if ($photo->caption)
                        <p class="small text-muted text-center mt-2 mb-0">{{ $photo->caption }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
