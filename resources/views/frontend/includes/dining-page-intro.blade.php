<div class="dining-page-intro text-center wow fadeInUp">
    @if (! empty($setting->dining_intro))
        <div class="dining-page-intro__prose mx-auto mb-4">
            {!! $setting->dining_intro !!}
        </div>
    @else
        <h2 class="dining-page-intro__title">Garden-to-table dining in Musanze</h2>
        <p class="dining-page-intro__lead mx-auto mb-4">Rwandan flavours and international comfort — ingredients from our ecological garden and local farmers, steps from Volcanoes National Park.</p>
    @endif

    <ul class="dining-page-chips list-unstyled d-flex flex-wrap justify-content-center gap-2 gap-md-3 mb-0">
        <li class="dining-page-chip"><i class="fas fa-seedling" aria-hidden="true"></i><span>Garden produce</span></li>
        <li class="dining-page-chip"><i class="fas fa-utensils" aria-hidden="true"></i><span>Rwandan &amp; international</span></li>
        <li class="dining-page-chip"><i class="fas fa-wallet" aria-hidden="true"></i><span>Pay at the hotel</span></li>
    </ul>
</div>
