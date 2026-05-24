@php
    $heading = $setting->flexible_stay_heading ?: 'Flexible Stay Options Designed Around You';
    $subheading = $setting->flexible_stay_subheading ?: 'Choose the right setup for your trip — from short stays to extended visits.';

    $cards = [
        [
            'title' => $setting->flexible_stay_card1_title ?: 'Flexible Room Choices',
            'text' => $setting->flexible_stay_card1_text ?: 'Choose the room size and setup that fits your stay.',
            'icon' => $setting->flexible_stay_card1_icon ?: 'fas fa-home',
        ],
        [
            'title' => $setting->flexible_stay_card2_title ?: 'Optional Kitchen Access',
            'text' => $setting->flexible_stay_card2_text ?: 'Available for guests who prefer cooking or long-term stays.',
            'icon' => $setting->flexible_stay_card2_icon ?: 'fas fa-utensils',
        ],
        [
            'title' => $setting->flexible_stay_card3_title ?: 'Perfect for Families & Groups',
            'text' => $setting->flexible_stay_card3_text ?: 'Combine rooms and share living spaces comfortably.',
            'icon' => $setting->flexible_stay_card3_icon ?: 'fas fa-users',
        ],
    ];

    $flexBg = ! empty($setting->flexible_stay_bg_image ?? null)
        ? asset('storage/images/pages/' . ltrim($setting->flexible_stay_bg_image, '/'))
        : null;
@endphp

<section class="flexible-stay-band parallax-bg rel z-1" @if($flexBg) style="background-image: url('{{ $flexBg }}');" @endif>
    <div class="container container-1130 rel z-2">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="section-title text-center mb-40 wow fadeInUp delay-0-2s">
                    <h2>{{ $heading }}</h2>
                    <p class="mt-15">{{ $subheading }}</p>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center flexible-stay-grid">
            @foreach ($cards as $idx => $card)
                <div class="col-lg-4 col-md-6">
                    <div class="flexible-stay-card wow fadeInUp delay-0-{{ 2 + $idx }}s">
                        <div class="d-flex align-items-center mb-20 flexible-stay-card__head">
                            <span class="flexible-stay-icon">
                                <i class="{{ $card['icon'] }} text-white"></i>
                            </span>
                            <h4 class="mb-0">{{ $card['title'] }}</h4>
                        </div>
                        <p class="mb-0">{{ $card['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-45">
            <a href="{{ route('room.booking') }}" class="theme-btn">
                Book Now <i class="far fa-angle-right"></i>
            </a>
        </div>
    </div>
</section>
