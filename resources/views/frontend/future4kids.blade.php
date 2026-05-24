@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'future4kids'])

<section class="isange-section isange-section--cream rel z-1">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7 wow fadeInUp delay-0-2s">
                <span class="isange-section__eyebrow">Our mission</span>
                <h2>Stay with purpose. Explore with impact.</h2>
                <p class="lead mb-4">Future 4 Kids created Isange Paradise Eco Resort as a sustainable funding engine for vulnerable children and families in Musanze.</p>
                <p><strong>100% of resort profits</strong> support education, healthcare, skills development, and women’s empowerment.</p>
                <ul class="isange-purpose-list isange-purpose-list--compact">
                    <li><i class="fas fa-graduation-cap" aria-hidden="true"></i> Education support for children</li>
                    <li><i class="fas fa-heartbeat" aria-hidden="true"></i> Community healthcare outreach</li>
                    <li><i class="fas fa-tools" aria-hidden="true"></i> Skills development &amp; capacity building</li>
                    <li><i class="fas fa-female" aria-hidden="true"></i> Women empowerment initiatives</li>
                    <li><i class="fas fa-users" aria-hidden="true"></i> Meaningful local employment at the resort</li>
                </ul>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="https://www.future4kids.at/" class="theme-btn" target="_blank" rel="noopener noreferrer">
                        Visit Future 4 Kids website <i class="fas fa-external-link-alt"></i>
                    </a>
                    <a href="{{ route('room.booking') }}" class="theme-btn style-three">Book and support our mission <i class="far fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-5 wow fadeInUp delay-0-3s">
                <div class="isange-impact-card isange-impact-card--clean p-4 p-md-5">
                    <h3>Travel that gives back</h3>
                    <p class="mb-0">Every night at Isange Paradise directly supports Future 4 Kids programs. Conscious travel makes a measurable difference in Rwanda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="isange-section rel z-1 bgc-white" id="shop">
    <div class="container">
        <div class="row justify-content-center text-center mb-45 wow fadeInUp">
            <div class="col-lg-7">
                <span class="isange-section__eyebrow">On-site shop</span>
                <h2>Future 4 Kids Shop</h2>
                <p class="mb-0">Handmade Rwandan fashion, crafts, and accessories from artisans in our programs — available during your stay.</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ([
                ['icon' => 'fa-tshirt', 'title' => 'Made in Rwanda clothing', 'text' => 'Locally designed and produced apparel.'],
                ['icon' => 'fa-gem', 'title' => 'Handmade accessories', 'text' => 'Jewelry, bags, and crafts by skilled artisans.'],
                ['icon' => 'fa-gift', 'title' => 'Gifts & souvenirs', 'text' => 'Meaningful mementos from your Rwanda journey.'],
                ['icon' => 'fa-store', 'title' => 'Community-made products', 'text' => 'Every purchase empowers local families.'],
            ] as $item)
            <div class="col-md-6 col-lg-3 wow fadeInUp delay-0-2s">
                <div class="isange-why-card isange-why-card--minimal h-100">
                    <div class="isange-why-card__icon"><i class="fas {{ $item['icon'] }}" aria-hidden="true"></i></div>
                    <h3 class="h6">{{ $item['title'] }}</h3>
                    <p class="mb-0 small text-muted">{{ $item['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-50">
            <p class="text-muted mb-3">Visit the shop during your stay, or contact us for availability.</p>
            <a href="{{ route('contact') }}" class="theme-btn style-three btn-sm">Contact us <i class="far fa-angle-right"></i></a>
        </div>
    </div>
</section>

@include('frontend.includes.youtube-stories-widget', ['variant' => 'cream'])

@endsection
