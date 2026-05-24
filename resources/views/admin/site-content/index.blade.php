@extends('layouts.adminbase')

@section('title', 'Website content')

@section('sidebar')
    @parent
@endsection

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h1 class="h3 mb-1">Website content</h1>
                        <p class="text-muted mb-0">Edit banners, captions, and page copy. Dynamic lists (rooms, menu, gallery) stay in their own admin sections.</p>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success mb-0 py-2">{{ session('success') }}</div>
                    @endif
                </div>

                <form action="{{ route('siteContent.save') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <ul class="nav nav-tabs flex-wrap" id="siteContentTabs" role="tablist">
                        @foreach ($tabOrder as $i => $tabKey)
                            @if ($headers->has($tabKey))
                                @php $label = $headers[$tabKey]->label; @endphp
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $i === 0 ? 'active' : '' }}" id="tab-{{ $tabKey }}-btn" data-bs-toggle="tab" data-bs-target="#tab-{{ $tabKey }}" type="button" role="tab">{{ $label }}</button>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    <div class="tab-content border border-top-0 bg-white p-4 rounded-bottom shadow-sm" id="siteContentTabsContent">
                        @foreach ($tabOrder as $i => $tabKey)
                            @if (! $headers->has($tabKey))
                                @continue
                            @endif
                            @php
                                $page = \App\Support\PageContent::get($tabKey, $headers);
                                $s = $page['sections'];
                            @endphp
                            <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="tab-{{ $tabKey }}" role="tabpanel">

                                @if ($tabKey !== 'global')
                                    @include('admin.site-content._banner-fields', ['pageKey' => $tabKey, 'pageData' => $page])
                                @endif

                                @if (in_array($tabKey, ['home', 'about', 'rooms', 'experiences', 'future4kids', 'contact', 'facilities', 'dining', 'services', 'gallery', 'blogs', 'booking', 'terms', 'global'], true))
                                    <div class="mb-3">
                                        <label class="form-label">Intro / lead text</label>
                                        <textarea class="form-control js-summernote" name="pages[{{ $tabKey }}][intro_html]" rows="4">{{ old("pages.{$tabKey}.intro_html", $page['intro_html']) }}</textarea>
                                    </div>
                                @endif

                                @if ($tabKey === 'global')
                                    <div class="mb-3">
                                        <label class="form-label">Header location line</label>
                                        <input type="text" class="form-control" name="pages[global][sections][header_tagline]" value="{{ old('pages.global.sections.header_tagline', $s['header_tagline'] ?? '') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Footer description</label>
                                        <textarea class="form-control" name="pages[global][sections][footer_blurb]" rows="3">{{ old('pages.global.sections.footer_blurb', $s['footer_blurb'] ?? '') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Amenities band — title</label>
                                        <input type="text" class="form-control" name="pages[global][sections][amenities_title]" value="{{ old('pages.global.sections.amenities_title', $s['amenities_title'] ?? '') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Amenities band — lead</label>
                                        <textarea class="form-control" name="pages[global][sections][amenities_lead]" rows="2">{{ old('pages.global.sections.amenities_lead', $s['amenities_lead'] ?? '') }}</textarea>
                                    </div>
                                    @php $amenityItems = $s['amenities_items'] ?? []; @endphp
                                    <p class="small text-muted">Amenities cards (emoji, title, text, link path e.g. /dining)</p>
                                    @for ($a = 0; $a < 9; $a++)
                                        @php $item = $amenityItems[$a] ?? ['emoji'=>'','title'=>'','text'=>'','href'=>'']; @endphp
                                        <div class="row g-2 mb-2 border-bottom pb-2">
                                            <div class="col-md-1"><input type="text" class="form-control form-control-sm" name="pages[global][sections][amenities_items][{{ $a }}][emoji]" value="{{ $item['emoji'] ?? '' }}" placeholder="🌿"></div>
                                            <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="pages[global][sections][amenities_items][{{ $a }}][title]" value="{{ $item['title'] ?? '' }}" placeholder="Title"></div>
                                            <div class="col-md-4"><input type="text" class="form-control form-control-sm" name="pages[global][sections][amenities_items][{{ $a }}][text]" value="{{ $item['text'] ?? '' }}" placeholder="Description"></div>
                                            <div class="col-md-4"><input type="text" class="form-control form-control-sm" name="pages[global][sections][amenities_items][{{ $a }}][href]" value="{{ $item['href'] ?? '' }}" placeholder="/dining"></div>
                                        </div>
                                    @endfor
                                @endif

                                @if ($tabKey === 'home')
                                    <hr>
                                    <h6 class="fw-semibold">Home page sections</h6>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-4"><label class="form-label small">About block — eyebrow</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][about_eyebrow]" value="{{ $s['about_eyebrow'] ?? '' }}"></div>
                                        <div class="col-md-8"><label class="form-label small">About block — title</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][about_title]" value="{{ $s['about_title'] ?? '' }}"></div>
                                        <div class="col-md-4"><label class="form-label small">Accommodation — eyebrow</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][accommodation_eyebrow]" value="{{ $s['accommodation_eyebrow'] ?? '' }}"></div>
                                        <div class="col-md-8"><label class="form-label small">Accommodation — title</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][accommodation_title]" value="{{ $s['accommodation_title'] ?? '' }}"></div>
                                        <div class="col-12"><label class="form-label small">Accommodation intro</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][accommodation_intro]" value="{{ $s['accommodation_intro'] ?? '' }}"></div>
                                        <div class="col-12"><label class="form-label small">Accommodation footnote</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][accommodation_footnote]" value="{{ $s['accommodation_footnote'] ?? '' }}"></div>
                                        <div class="col-md-4"><label class="form-label small">Why stay — eyebrow</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][why_eyebrow]" value="{{ $s['why_eyebrow'] ?? '' }}"></div>
                                        <div class="col-md-8"><label class="form-label small">Why stay — title</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][why_title]" value="{{ $s['why_title'] ?? '' }}"></div>
                                    </div>
                                    <p class="small text-muted mb-2">Why stay cards (icon class e.g. fa-leaf)</p>
                                    @for ($w = 0; $w < 4; $w++)
                                        @php $card = ($s['why_cards'] ?? [])[$w] ?? []; @endphp
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="pages[home][sections][why_cards][{{ $w }}][icon]" value="{{ $card['icon'] ?? '' }}" placeholder="fa-leaf"></div>
                                            <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="pages[home][sections][why_cards][{{ $w }}][title]" value="{{ $card['title'] ?? '' }}" placeholder="Title"></div>
                                            <div class="col-md-7"><input type="text" class="form-control form-control-sm" name="pages[home][sections][why_cards][{{ $w }}][text]" value="{{ $card['text'] ?? '' }}" placeholder="Text"></div>
                                        </div>
                                    @endfor
                                    <div class="row g-2 mt-2">
                                        <div class="col-md-4"><label class="form-label small">Experiences — eyebrow</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][experiences_eyebrow]" value="{{ $s['experiences_eyebrow'] ?? '' }}"></div>
                                        <div class="col-md-8"><label class="form-label small">Experiences — title</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][experiences_title]" value="{{ $s['experiences_title'] ?? '' }}"></div>
                                        <div class="col-12"><label class="form-label small">Experiences intro</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][experiences_intro]" value="{{ $s['experiences_intro'] ?? '' }}"></div>
                                        <div class="col-md-6"><label class="form-label small">CTA — title</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][cta_title]" value="{{ $s['cta_title'] ?? '' }}"></div>
                                        <div class="col-md-6"><label class="form-label small">CTA — text</label><input type="text" class="form-control form-control-sm" name="pages[home][sections][cta_text]" value="{{ $s['cta_text'] ?? '' }}"></div>
                                    </div>
                                    <p class="small text-muted mt-3 mb-0">Home hero slides: use <strong>Home slider</strong> in the sidebar.</p>
                                @endif

                                @if ($tabKey === 'about')
                                    <hr>
                                    <div class="row g-2">
                                        <div class="col-md-4"><label class="form-label small">Story eyebrow</label><input type="text" class="form-control form-control-sm" name="pages[about][sections][story_eyebrow]" value="{{ $s['story_eyebrow'] ?? '' }}"></div>
                                        <div class="col-md-8"><label class="form-label small">Story title</label><input type="text" class="form-control form-control-sm" name="pages[about][sections][story_title]" value="{{ $s['story_title'] ?? '' }}"></div>
                                        <div class="col-md-4"><label class="form-label small">Team eyebrow</label><input type="text" class="form-control form-control-sm" name="pages[about][sections][team_eyebrow]" value="{{ $s['team_eyebrow'] ?? '' }}"></div>
                                        <div class="col-md-8"><label class="form-label small">Team title</label><input type="text" class="form-control form-control-sm" name="pages[about][sections][team_title]" value="{{ $s['team_title'] ?? '' }}"></div>
                                        <div class="col-12"><label class="form-label small">Team intro</label><textarea class="form-control form-control-sm" name="pages[about][sections][team_intro]" rows="2">{{ $s['team_intro'] ?? '' }}</textarea></div>
                                    </div>
                                    <p class="small text-muted mt-2">Extended welcome text can also be edited under <strong>About content</strong> or in intro above.</p>
                                @endif

                                @if ($tabKey === 'experiences')
                                    <hr>
                                    <p class="small text-muted">Experience cards</p>
                                    @php $items = $s['items'] ?? []; @endphp
                                    @for ($e = 0; $e < 9; $e++)
                                        @php $item = $items[$e] ?? []; @endphp
                                        <div class="row g-2 mb-2 border-bottom pb-2">
                                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="pages[experiences][sections][items][{{ $e }}][id]" value="{{ $item['id'] ?? '' }}" placeholder="id"></div>
                                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="pages[experiences][sections][items][{{ $e }}][icon]" value="{{ $item['icon'] ?? '' }}" placeholder="fa-paw"></div>
                                            <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="pages[experiences][sections][items][{{ $e }}][title]" value="{{ $item['title'] ?? '' }}" placeholder="Title"></div>
                                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="pages[experiences][sections][items][{{ $e }}][text]" value="{{ $item['text'] ?? '' }}" placeholder="Description"></div>
                                        </div>
                                    @endfor
                                @endif

                                @if ($tabKey === 'future4kids')
                                    <hr>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-4"><input type="text" class="form-control form-control-sm" name="pages[future4kids][sections][mission_eyebrow]" value="{{ $s['mission_eyebrow'] ?? '' }}" placeholder="Mission eyebrow"></div>
                                        <div class="col-md-8"><input type="text" class="form-control form-control-sm" name="pages[future4kids][sections][mission_title]" value="{{ $s['mission_title'] ?? '' }}" placeholder="Mission title"></div>
                                        <div class="col-12"><textarea class="form-control form-control-sm" name="pages[future4kids][sections][mission_lead]" rows="2" placeholder="Mission lead">{{ $s['mission_lead'] ?? '' }}</textarea></div>
                                        <div class="col-12"><textarea class="form-control form-control-sm js-summernote-sm" name="pages[future4kids][sections][mission_text]" rows="2">{{ $s['mission_text'] ?? '' }}</textarea></div>
                                    </div>
                                    <label class="form-label small">Mission bullets (one per line)</label>
                                    <textarea class="form-control mb-3" name="pages[future4kids][sections][mission_bullets_text]" rows="5">@foreach ($s['mission_bullets'] ?? [] as $bullet){{ $bullet }}
@endforeach</textarea>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6"><input type="text" class="form-control form-control-sm" name="pages[future4kids][sections][impact_title]" value="{{ $s['impact_title'] ?? '' }}" placeholder="Impact box title"></div>
                                        <div class="col-md-6"><textarea class="form-control form-control-sm" name="pages[future4kids][sections][impact_text]" rows="2">{{ $s['impact_text'] ?? '' }}</textarea></div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-4"><input type="text" class="form-control form-control-sm" name="pages[future4kids][sections][shop_eyebrow]" value="{{ $s['shop_eyebrow'] ?? '' }}" placeholder="Shop eyebrow"></div>
                                        <div class="col-md-8"><input type="text" class="form-control form-control-sm" name="pages[future4kids][sections][shop_title]" value="{{ $s['shop_title'] ?? '' }}" placeholder="Shop title"></div>
                                        <div class="col-12"><textarea class="form-control form-control-sm" name="pages[future4kids][sections][shop_intro]" rows="2">{{ $s['shop_intro'] ?? '' }}</textarea></div>
                                    </div>
                                    @for ($sh = 0; $sh < 4; $sh++)
                                        @php $shop = ($s['shop_items'] ?? [])[$sh] ?? []; @endphp
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="pages[future4kids][sections][shop_items][{{ $sh }}][icon]" value="{{ $shop['icon'] ?? '' }}"></div>
                                            <div class="col-md-4"><input type="text" class="form-control form-control-sm" name="pages[future4kids][sections][shop_items][{{ $sh }}][title]" value="{{ $shop['title'] ?? '' }}"></div>
                                            <div class="col-md-6"><input type="text" class="form-control form-control-sm" name="pages[future4kids][sections][shop_items][{{ $sh }}][text]" value="{{ $shop['text'] ?? '' }}"></div>
                                        </div>
                                    @endfor
                                @endif

                                @if (in_array($tabKey, ['rooms', 'facilities', 'dining', 'contact', 'booking', 'services', 'gallery', 'blogs'], true))
                                    <div class="mb-3">
                                        <label class="form-label">Page body / extra copy</label>
                                        <textarea class="form-control js-summernote" name="pages[{{ $tabKey }}][body_html]" rows="5">{{ old("pages.{$tabKey}.body_html", $page['body_html']) }}</textarea>
                                        @if ($tabKey === 'facilities')
                                            <p class="small text-muted mt-1">Facility intro also editable in <strong>Site settings → Facilities landing</strong>. Individual facilities in <strong>Facilities</strong>.</p>
                                        @endif
                                        @if ($tabKey === 'dining')
                                            <p class="small text-muted mt-1">Menu items: <strong>Restaurant menu</strong>. Dining intro in <strong>Site settings</strong>.</p>
                                        @endif
                                    </div>
                                @endif

                                @if ($tabKey === 'terms')
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Terms &amp; conditions text is edited in one place:
                                        <a href="{{ route('setting') }}#tab-terms" class="alert-link fw-semibold">Site settings → Terms &amp; conditions</a>.
                                        Use the banner fields above for the page heading, caption, and hero image only.
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-1"></i> Save all website content</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        if ($.fn.summernote) {
            $('.js-summernote').summernote({ height: 180 });
        }
    });
</script>
@endsection
