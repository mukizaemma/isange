@extends('layouts.adminbase')

@section('title', 'Site settings')

@section('sidebar')
    @parent
@endsection

@section('content')
<style>
    .ma-settings-page .card.ma-settings-card {
        border: 1px solid rgba(230, 145, 56, 0.35);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .ma-settings-page .ma-settings-header {
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        color: #f5e6d3;
        padding: 1.25rem 1.5rem;
        border-bottom: 3px solid #e69138;
    }
    .ma-settings-page .ma-settings-header h1 {
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: 0.02em;
    }
    .ma-settings-page .ma-settings-header .text-accent {
        color: #e69138;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }
    .ma-settings-page .nav-tabs.ma-tabs {
        border-bottom: 2px solid #eee;
        padding: 0 1rem;
        margin-bottom: 0;
        background: #faf7f2;
    }
    .ma-settings-page .nav-tabs.ma-tabs .nav-link {
        border: none;
        border-radius: 0;
        color: #444;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.85rem 1.1rem;
        margin-bottom: -2px;
    }
    .ma-settings-page .nav-tabs.ma-tabs .nav-link:hover {
        color: #c2782f;
        border-color: transparent;
    }
    .ma-settings-page .nav-tabs.ma-tabs .nav-link.active {
        color: #0a0a0a;
        background: #fff;
        border-bottom: 3px solid #e69138;
    }
    .ma-settings-page .tab-content.ma-tab-body {
        padding: 1.75rem 1.5rem 1.25rem;
        background: #fff;
    }
    .ma-settings-page .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #333;
        margin-bottom: 0.35rem;
    }
    .ma-settings-page .form-control:focus {
        border-color: #e69138;
        box-shadow: 0 0 0 0.2rem rgba(230, 145, 56, 0.15);
    }
    .ma-settings-page .ma-save-bar {
        background: #faf7f2;
        border-top: 1px solid rgba(230, 145, 56, 0.25);
        padding: 1rem 1.5rem;
    }
    .ma-settings-page .btn-ma-primary {
        background: #e69138;
        border-color: #e69138;
        color: #0a0a0a;
        font-weight: 700;
        padding: 0.5rem 1.35rem;
        border-radius: 8px;
    }
    .ma-settings-page .btn-ma-primary:hover {
        background: #c2782f;
        border-color: #c2782f;
        color: #0a0a0a;
    }
    .ma-settings-page .note-editor.note-frame {
        border-radius: 8px;
        border-color: #ddd !important;
    }
    .ma-settings-page .alert-ma-success {
        background: rgba(230, 145, 56, 0.12);
        border: 1px solid rgba(230, 145, 56, 0.45);
        color: #3d2910;
        border-radius: 8px;
    }
</style>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="content ma-settings-page">
                <div class="container-fluid py-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card ma-settings-card">
                                <div class="ma-settings-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div>
                                        <div class="text-accent mb-1">Isange Paradise · Configuration</div>
                                        <h1>Site settings</h1>
                                    </div>
                                    @if (session()->has('success'))
                                        <div class="alert alert-ma-success alert-dismissible fade show mb-0 py-2 px-3" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                </div>

                                <form action="{{ route('saveSetting') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <ul class="nav nav-tabs ma-tabs" id="settingsTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="tab-contact-btn" data-bs-toggle="tab" data-bs-target="#tab-contact" type="button" role="tab">Contact &amp; logo</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab-about-btn" data-bs-toggle="tab" data-bs-target="#tab-about" type="button" role="tab">About the hotel</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab-terms-btn" data-bs-toggle="tab" data-bs-target="#tab-terms" type="button" role="tab">Terms &amp; conditions</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab-otas-btn" data-bs-toggle="tab" data-bs-target="#tab-otas" type="button" role="tab">OTAs &amp; map</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab-facilities-page-btn" data-bs-toggle="tab" data-bs-target="#tab-facilities-page" type="button" role="tab">Facilities landing</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab-flexible-stay-btn" data-bs-toggle="tab" data-bs-target="#tab-flexible-stay" type="button" role="tab">Flexible Stay section</button>
                                        </li>
                                    </ul>

                                    <div class="tab-content ma-tab-body">
                                        {{-- Contact --}}
                                        <div class="tab-pane fade show active" id="tab-contact" role="tabpanel">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="company">Company name</label>
                                                    <input type="text" class="form-control" id="company" name="company" value="{{ old('company', $data->company) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="address">Address</label>
                                                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $data->address) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="phone">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $data->phone) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $data->email) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="keywords">Meta keywords <span class="text-muted fw-normal">(SEO)</span></label>
                                                    <input type="text" class="form-control" id="keywords" name="keywords" value="{{ old('keywords', $data->keywords) }}" placeholder="Optional">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="usd_to_rwf_rate">USD → RWF rate</label>
                                                    <input type="number" step="0.01" min="1" class="form-control" id="usd_to_rwf_rate" name="usd_to_rwf_rate" value="{{ old('usd_to_rwf_rate', $data->usd_to_rwf_rate ?? 1300) }}">
                                                    <div class="form-text">Used for RWF hint when guests hover USD prices.</div>
                                                </div>
                                            </div>

                                            <hr class="my-4 opacity-25">

                                            <p class="small text-uppercase fw-bold text-muted mb-3" style="letter-spacing: 0.08em;">Social profiles</p>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label" for="facebook">Facebook URL</label>
                                                    <input type="url" class="form-control" id="facebook" name="facebook" value="{{ old('facebook', $data->facebook) }}" placeholder="https://">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="instagram">Instagram URL</label>
                                                    <input type="url" class="form-control" id="instagram" name="instagram" value="{{ old('instagram', $data->instagram) }}" placeholder="https://">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="twitter">Twitter / X URL</label>
                                                    <input type="url" class="form-control" id="twitter" name="twitter" value="{{ old('twitter', $data->twitter) }}" placeholder="https://">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="youtube">YouTube URL</label>
                                                    <input type="url" class="form-control" id="youtube" name="youtube" value="{{ old('youtube', $data->youtube) }}" placeholder="https://">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="tiktok">TikTok URL</label>
                                                    <input type="url" class="form-control" id="tiktok" name="tiktok" value="{{ old('tiktok', $data->tiktok ?? '') }}" placeholder="https://">
                                                </div>
                                            </div>

                                            <hr class="my-4 opacity-25">

                                            <div class="row g-4 align-items-start">
                                                <div class="col-md-5">
                                                    <label class="form-label">Current logo</label>
                                                    <div class="border rounded p-3 bg-light text-center" style="min-height: 120px;">
                                                        @if (! empty($data->logo))
                                                            <img src="{{ asset('storage/images/' . ltrim($data->logo, '/')) }}" alt="Logo" class="img-fluid" style="max-height: 110px;">
                                                        @else
                                                            <span class="text-muted small">No logo uploaded.</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <label class="form-label" for="logo">Upload new logo</label>
                                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                                    <div class="form-text">Recommended around <strong>120×90</strong> px or similar ratio; PNG or JPG.</div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- About --}}
                                        <div class="tab-pane fade" id="tab-about" role="tabpanel">
                                            <label class="form-label" for="setting_welcome">Welcome / about text</label>
                                            <textarea id="setting_welcome" name="welcome" class="form-control" rows="8">{{ old('welcome', $about->welcome) }}</textarea>
                                            <p class="form-text mt-2 mb-4">Shown on the public site where you feature the hotel story.</p>

                                            <label class="form-label" for="setting_background">Additional details <span class="text-muted fw-normal">(optional)</span></label>
                                            <textarea id="setting_background" name="background" class="form-control" rows="6">{{ old('background', $about->background) }}</textarea>
                                            <p class="form-text mt-2">Supporting copy or mission-style content.</p>
                                        </div>

                                        {{-- Terms --}}
                                        <div class="tab-pane fade" id="tab-terms" role="tabpanel">
                                            <label class="form-label" for="setting_terms">Terms &amp; conditions</label>
                                            <textarea id="setting_terms" name="terms" class="form-control" rows="12">{{ old('terms', $about->terms) }}</textarea>
                                            <p class="form-text mt-2">Displayed on your terms page on the website.</p>
                                        </div>

                                        {{-- OTAs & map --}}
                                        <div class="tab-pane fade" id="tab-otas" role="tabpanel">
                                            <p class="text-muted small mb-4"><strong>Book on OTA</strong> uses Booking.com, Expedia, and Emerging Travel Group. <strong>Reviews</strong> use TripAdvisor and Google Business Profile. Use full URLs (including <code>https://</code>).</p>
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label" for="booking_engine_url">Direct booking engine (pay online)</label>
                                                    <input type="url" class="form-control" id="booking_engine_url" name="booking_engine_url" value="{{ old('booking_engine_url', $data->booking_engine_url ?? '') }}" placeholder="https://your-booking-engine.example/...">
                                                    <p class="form-text">Opens in a new tab from <strong>Book Your Stay</strong> in the header, <strong>Book and pay now</strong>, and <strong>Direct</strong> in the footer booking block.</p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" for="url_booking">Booking.com (book on OTA)</label>
                                                    <input type="url" class="form-control" id="url_booking" name="url_booking" value="{{ old('url_booking', $data->url_booking ?? '') }}" placeholder="https://www.booking.com/hotel/...">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" for="url_expedia">Expedia (book on OTA)</label>
                                                    <input type="url" class="form-control" id="url_expedia" name="url_expedia" value="{{ old('url_expedia', $data->url_expedia ?? '') }}" placeholder="https://www.expedia.com/...">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" for="url_emerging_travel">Emerging Travel Group (book on OTA)</label>
                                                    <input type="url" class="form-control" id="url_emerging_travel" name="url_emerging_travel" value="{{ old('url_emerging_travel', $data->url_emerging_travel ?? '') }}" placeholder="https://">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" for="url_tripadvisor">TripAdvisor (reviews)</label>
                                                    <input type="url" class="form-control" id="url_tripadvisor" name="url_tripadvisor" value="{{ old('url_tripadvisor', $data->url_tripadvisor ?? '') }}" placeholder="https://www.tripadvisor.com/...">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" for="url_google_business">Google Business Profile (reviews)</label>
                                                    <input type="url" class="form-control" id="url_google_business" name="url_google_business" value="{{ old('url_google_business', $data->url_google_business ?? '') }}" placeholder="https://g.page/... or Maps link">
                                                </div>
                                            </div>

                                            <hr class="my-4 opacity-25">

                                            <label class="form-label" for="google_map_embed">Google Maps embed code</label>
                                            <textarea id="google_map_embed" name="google_map_embed" class="form-control font-monospace small" rows="8" placeholder='Paste the full iframe embed code from Google Maps ("Share" → "Embed a map").'>{{ old('google_map_embed', $data->google_map_embed ?? '') }}</textarea>
                                            <p class="form-text">Paste the entire <code>&lt;iframe&gt;...&lt;/iframe&gt;</code> snippet.</p>

                                            <hr class="my-4 opacity-25">

                                            <label class="form-label" for="youtube_stories_embed">YouTube Stories / Shorts embed</label>
                                            <textarea id="youtube_stories_embed" name="youtube_stories_embed" class="form-control font-monospace small" rows="6" placeholder='Paste a YouTube iframe embed (Shorts, playlist, or channel widget).'>{{ old('youtube_stories_embed', $data->youtube_stories_embed ?? '') }}</textarea>
                                            <p class="form-text">Shown in page sidebars and empty sections across the site. If empty, a link to your YouTube profile is used instead.</p>
                                        </div>

                                        <div class="tab-pane fade" id="tab-facilities-page" role="tabpanel">
                                            <p class="text-muted small mb-4">Top section of the public <strong>Facilities</strong> page (before the facility cards).</p>
                                            <div class="row g-4 mb-4">
                                                <div class="col-md-5">
                                                    <label class="form-label">Hero image</label>
                                                    @if (! empty($data->facilities_hero_image))
                                                        <div class="mb-2 border rounded p-2 bg-light">
                                                            <img src="{{ asset('storage/images/pages/' . $data->facilities_hero_image) }}" alt="" class="img-fluid rounded">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control" name="facilities_hero_image" accept="image/*">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label" for="setting_facilities_intro">Introduction</label>
                                                    <textarea id="setting_facilities_intro" name="facilities_intro" class="form-control" rows="10">{{ old('facilities_intro', $data->facilities_intro ?? '') }}</textarea>
                                                    <p class="form-text mt-2">Shown below the banner on the Facilities page.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="tab-flexible-stay" role="tabpanel">
                                            <p class="text-muted small mb-4">Controls the “Flexible Stay Options Designed Around You” section on the Home and Rooms pages.</p>

                                            <div class="row g-4 mb-4">
                                                <div class="col-md-5">
                                                    <label class="form-label">Background image</label>
                                                    @if (! empty($data->flexible_stay_bg_image))
                                                        <div class="mb-2 border rounded p-2 bg-light">
                                                            <img src="{{ asset('storage/images/pages/' . $data->flexible_stay_bg_image) }}" alt="" class="img-fluid rounded">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control" name="flexible_stay_bg_image" accept="image/*">
                                                    <div class="form-text">Recommended: wide landscape image, at least <strong>1600×900</strong>.</div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label" for="flexible_stay_heading">Heading</label>
                                                            <input type="text" class="form-control" id="flexible_stay_heading" name="flexible_stay_heading" value="{{ old('flexible_stay_heading', $data->flexible_stay_heading ?? '') }}" placeholder="Flexible Stay Options Designed Around You">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="flexible_stay_subheading">Subheading</label>
                                                            <textarea class="form-control" id="flexible_stay_subheading" name="flexible_stay_subheading" rows="3" placeholder="Choose the right setup for your trip — from short stays to extended visits.">{{ old('flexible_stay_subheading', $data->flexible_stay_subheading ?? '') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="my-4 opacity-25">

                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <p class="small text-uppercase fw-bold text-muted mb-2" style="letter-spacing: 0.08em;">Card 1</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="flexible_stay_card1_title">Title</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card1_title" name="flexible_stay_card1_title" value="{{ old('flexible_stay_card1_title', $data->flexible_stay_card1_title ?? '') }}" placeholder="Flexible Room Choices">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label" for="flexible_stay_card1_text">Text</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card1_text" name="flexible_stay_card1_text" value="{{ old('flexible_stay_card1_text', $data->flexible_stay_card1_text ?? '') }}" placeholder="Choose the room size and setup that fits your stay.">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="flexible_stay_card1_icon">Icon (Font Awesome class)</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card1_icon" name="flexible_stay_card1_icon" value="{{ old('flexible_stay_card1_icon', $data->flexible_stay_card1_icon ?? '') }}" placeholder="fas fa-home">
                                                </div>

                                                <div class="col-12 mt-3">
                                                    <p class="small text-uppercase fw-bold text-muted mb-2" style="letter-spacing: 0.08em;">Card 2</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="flexible_stay_card2_title">Title</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card2_title" name="flexible_stay_card2_title" value="{{ old('flexible_stay_card2_title', $data->flexible_stay_card2_title ?? '') }}" placeholder="Optional Kitchen Access">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label" for="flexible_stay_card2_text">Text</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card2_text" name="flexible_stay_card2_text" value="{{ old('flexible_stay_card2_text', $data->flexible_stay_card2_text ?? '') }}" placeholder="Available for guests who prefer cooking or long-term stays.">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="flexible_stay_card2_icon">Icon (Font Awesome class)</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card2_icon" name="flexible_stay_card2_icon" value="{{ old('flexible_stay_card2_icon', $data->flexible_stay_card2_icon ?? '') }}" placeholder="fas fa-utensils">
                                                </div>

                                                <div class="col-12 mt-3">
                                                    <p class="small text-uppercase fw-bold text-muted mb-2" style="letter-spacing: 0.08em;">Card 3</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="flexible_stay_card3_title">Title</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card3_title" name="flexible_stay_card3_title" value="{{ old('flexible_stay_card3_title', $data->flexible_stay_card3_title ?? '') }}" placeholder="Perfect for Families &amp; Groups">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label" for="flexible_stay_card3_text">Text</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card3_text" name="flexible_stay_card3_text" value="{{ old('flexible_stay_card3_text', $data->flexible_stay_card3_text ?? '') }}" placeholder="Combine rooms and share living spaces comfortably.">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="flexible_stay_card3_icon">Icon (Font Awesome class)</label>
                                                    <input type="text" class="form-control" id="flexible_stay_card3_icon" name="flexible_stay_card3_icon" value="{{ old('flexible_stay_card3_icon', $data->flexible_stay_card3_icon ?? '') }}" placeholder="fas fa-users">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ma-save-bar d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <span class="text-muted small">All tabs save together.</span>
                                        <button type="submit" class="btn btn-ma-primary">
                                            <i class="fas fa-save me-2"></i>Save all changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var toolbar = [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ];

    function bootEditor($el, height, placeholder) {
        if (!$el.length || $el.data('sn-init')) return;
        $el.summernote({
            placeholder: placeholder || '',
            tabsize: 2,
            height: height || 220,
            toolbar: toolbar
        });
        $el.data('sn-init', true);
    }

    $(document).ready(function () {
        document.getElementById('tab-about-btn').addEventListener('shown.bs.tab', function () {
            bootEditor($('#setting_welcome'), 260, 'About your hotel…');
            bootEditor($('#setting_background'), 200, 'Optional extra detail…');
        });
        document.getElementById('tab-terms-btn').addEventListener('shown.bs.tab', function () {
            bootEditor($('#setting_terms'), 320, 'Terms and conditions…');
        });
        document.getElementById('tab-facilities-page-btn').addEventListener('shown.bs.tab', function () {
            bootEditor($('#setting_facilities_intro'), 240, 'Introduce your facilities…');
        });
    });
})();
</script>
@endsection
