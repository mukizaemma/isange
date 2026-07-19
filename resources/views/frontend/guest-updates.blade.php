@extends('layouts.frontbase')

@section('content')
@include('frontend.includes.page-header', ['pageKey' => 'blogs', 'title' => 'Recent guest updates'])

<section class="py-80 rpy-60">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div><span class="isange-section__eyebrow">Guest account</span><h2 class="mb-0">Recent updates</h2></div>
            <a class="theme-btn style-three" href="{{ route('booking.checkout') }}">Book on Discount</a>
        </div>
        <div class="row g-4">
            @forelse ($updates as $recipient)
                @php($update = $recipient->guestUpdate)
                @continue(! $update)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm overflow-hidden">
                        @if ($update->cover_image)<img class="card-img-top" src="{{ asset('storage/'.$update->cover_image) }}" alt="" loading="lazy">@endif
                        <div class="card-body p-4">
                            <small class="text-muted">{{ $recipient->sent_at?->format('M j, Y') }}</small>
                            <h3 class="h4 mt-2">{{ $update->title }}</h3>
                            <p class="mb-0">{!! nl2br(e($update->description)) !!}</p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><p class="text-center text-muted py-5">No guest updates have been sent to you yet.</p></div>
            @endforelse
        </div>
        <div class="mt-4">{{ $updates->links() }}</div>
    </div>
</section>
@endsection
