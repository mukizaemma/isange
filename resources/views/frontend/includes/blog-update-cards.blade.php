@foreach ($blogs as $blog)
    <div class="col-md-6 col-lg-4 wow fadeInUp delay-0-2s">
        <article class="isange-update-card h-100">
            <a href="{{ route('blog', $blog) }}" class="isange-update-card__image-link">
                <img src="{{ $blog->imageUrl() }}" alt="{{ $blog->title }}" class="isange-update-card__image" loading="lazy">
            </a>
            <div class="isange-update-card__body">
                <div class="isange-update-card__meta">
                    <span><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ optional($blog->published_at)->format('d M Y') }}</span>
                    <span><i class="far fa-eye" aria-hidden="true"></i> {{ number_format($blog->views ?? 0) }}</span>
                    @if (isset($blog->comments_count))
                        <span><i class="far fa-comments" aria-hidden="true"></i> {{ $blog->comments_count }}</span>
                    @endif
                </div>
                <h3 class="isange-update-card__title">
                    <a href="{{ route('blog', $blog) }}">{{ $blog->title }}</a>
                </h3>
                <p class="isange-update-card__excerpt">{{ $blog->excerpt() }}</p>
                <a href="{{ route('blog', $blog) }}" class="isange-update-card__more">
                    Read more <i class="fal fa-angle-right" aria-hidden="true"></i>
                </a>
            </div>
        </article>
    </div>
@endforeach
