@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => $post->title,
    'subtitle' => optional($post->published_at)->format('d M Y').' · '.$post->readingTimeMinutes().' min read',
    'imageUrl' => $post->imageUrl(),
])

<section class="isange-update-single isange-section rel z-1 bgc-white">
    <div class="container container-1130">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article class="isange-update-single__article">
                    <div class="isange-update-single__meta">
                        <span><i class="far fa-user" aria-hidden="true"></i> {{ $post->published_by ?? 'Isange Paradise' }}</span>
                        <span><i class="far fa-eye" aria-hidden="true"></i> {{ number_format($post->views ?? 0) }} views</span>
                        <span><i class="far fa-comments" aria-hidden="true"></i> {{ $post->comments->count() }} {{ $post->comments->count() === 1 ? 'comment' : 'comments' }}</span>
                    </div>

                    <div class="isange-update-single__hero mb-4">
                        <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="img-fluid rounded-3 w-100" loading="lazy">
                    </div>

                    <div class="welcome-prose isange-update-single__content">
                        {!! $post->body !!}
                    </div>
                </article>

                <section class="isange-update-comments mt-5 pt-4 border-top" id="comments">
                    <h3 class="isange-update-comments__title">Comments</h3>

                    @if (session('comment_success'))
                        <div class="alert alert-success" role="status">{{ session('comment_success') }}</div>
                    @endif

                    @if ($errors->has('submission') || $errors->has('cf-turnstile-response'))
                        <div class="alert alert-danger" role="alert">
                            {{ $errors->first('submission') ?: $errors->first('cf-turnstile-response') }}
                        </div>
                    @endif

                    @if ($post->comments->isNotEmpty())
                        <ul class="isange-update-comments__list list-unstyled mb-4">
                            @foreach ($post->comments as $comment)
                                <li class="isange-update-comments__item">
                                    <div class="isange-update-comments__author">{{ $comment->author_name }}</div>
                                    <time class="isange-update-comments__time" datetime="{{ $comment->created_at->toIso8601String() }}">
                                        {{ $comment->created_at->format('d M Y, H:i') }}
                                    </time>
                                    <p class="isange-update-comments__body mb-0">{{ $comment->body }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-4">Be the first to share your thoughts.</p>
                    @endif

                    <div class="isange-update-comments__form-wrap">
                        <h4 class="h5 mb-3">Leave a comment</h4>
                        <form action="{{ route('blog.comments.store', $post) }}" method="POST" class="isange-update-comments__form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="author_name">Your name</label>
                                    <input type="text" class="form-control @error('author_name') is-invalid @enderror" id="author_name" name="author_name" value="{{ old('author_name') }}" required maxlength="120">
                                    @error('author_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="author_email">Email <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="email" class="form-control @error('author_email') is-invalid @enderror" id="author_email" name="author_email" value="{{ old('author_email') }}" maxlength="255">
                                    @error('author_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="body">Comment</label>
                                    <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="4" required maxlength="5000">{{ old('body') }}</textarea>
                                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <x-human-form-fields />
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="theme-btn">Post comment <i class="far fa-angle-right"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>

                <p class="mt-5">
                    <a href="{{ route('blogs') }}" class="theme-btn style-three">Back to all updates <i class="far fa-angle-right"></i></a>
                </p>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="isange-update-related mt-5 pt-5 border-top">
                <h3 class="text-center mb-4">More updates</h3>
                <div class="row g-4">
                    @foreach ($related as $item)
                        <div class="col-md-4">
                            <article class="isange-update-card isange-update-card--compact h-100">
                                        <a href="{{ route('blog', $item) }}" class="isange-update-card__image-link">
                                    <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="isange-update-card__image" loading="lazy">
                                </a>
                                <div class="isange-update-card__body">
                                    <h4 class="isange-update-card__title h6">
                                        <a href="{{ route('blog', $item) }}">{{ $item->title }}</a>
                                    </h4>
                                    <a href="{{ route('blog', $item) }}" class="isange-update-card__more">Read more <i class="fal fa-angle-right"></i></a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

@endsection
