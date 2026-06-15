@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'dining'])

<section class="dining-page rel z-1">
    <div class="dining-page-band dining-page-band--cream py-100 rpy-70">
        <div class="dining-page__inner">
            @include('frontend.includes.dining-page-intro')
            @include('frontend.includes.dining-page-toolbar')
        </div>
    </div>

    <div class="dining-page-band dining-page-band--white py-100 rpy-70">
        <div class="dining-page__inner">
            @include('frontend.includes.dining-page-todays-section', [
                'todaysMenu' => $todaysMenu ?? [],
            ])
        </div>
    </div>

    <div class="dining-page-band dining-page-band--cream py-100 rpy-70">
        <div class="dining-page__inner">
            <div class="dining-full-menu wow fadeInUp">
                <div class="dining-page-section-head mb-4">
                    <span class="dining-cat-kicker">Explore</span>
                    <h2 class="dining-cat-title h4 mb-1" id="dining-full-menu-heading">Full menu</h2>
                    <p class="dining-page-section-head__sub small text-muted mb-0">Browse by category — send your order via WhatsApp or email when ready.</p>
                </div>

                <script type="application/json" id="dining-menu-data">@json($diningMenuColumns)</script>
                <div id="dining-menu-columns-app" class="dining-menu-columns-app">
                    <p class="text-center text-muted py-5 mb-0 d-none" id="dining-menu-empty">Menu coming soon.</p>
                    <div id="dining-menu-loaded" class="d-none">
                        <div class="dining-menu-tabs-sticky" id="dining-menu-tabs-sticky">
                            <div class="dining-menu-tabs-wrap" role="navigation" aria-label="Menu categories">
                                <div class="dining-menu-tabs nav nav-pills flex-nowrap gap-2" id="dining-menu-tabs" role="tablist"></div>
                            </div>
                        </div>
                        <div class="dining-menu-panel">
                            <div id="dining-menu-category-banner" class="dining-menu-category-banner d-none" aria-hidden="true">
                                <img class="dining-menu-category-banner__img" src="" alt="">
                                <div class="dining-menu-category-banner__overlay">
                                    <h3 class="dining-menu-category-banner__title mb-0"></h3>
                                </div>
                            </div>
                            <div class="dining-menu-grid-wrap">
                                <div class="dining-menu-items-grid" id="dining-menu-items-root" aria-live="polite"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend.includes.dining-order-ui')

@endsection

@push('scripts')
@include('frontend.includes.dining-order-scripts')
<script src="{{ asset('assets/js/dining-menu-page.js') }}" defer></script>
@endpush
