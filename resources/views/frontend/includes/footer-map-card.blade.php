@php
    $mapEmbed = trim((string) ($setting->google_map_embed ?? ''));
    $mapsLink = trim((string) ($setting->url_google_business ?? ''));
@endphp
<div class="ma-footer-map-card" aria-label="Resort location on map">
    <div class="ma-footer-map-card__embed">
        @if ($mapEmbed !== '')
            {!! $mapEmbed !!}
        @else
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d1282.6733953538446!2d29.34723022219446!3d-2.0577636813635376!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0x19dd291561adb953%3A0x6084750be3aaab83!2sDelta%20Resort%20Hotel%20Kibuye!3m2!1d-2.0575487!2d29.348358299999997!5e1!3m2!1sen!2srw!4v1738753640971!5m2!1sen!2srw"
                title="Isange Paradise location"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen=""
            ></iframe>
        @endif
    </div>
    @if ($mapsLink !== '')
        <a href="{{ $mapsLink }}" class="ma-footer-map-card__link" target="_blank" rel="noopener noreferrer">
            Open in Google Maps <i class="fas fa-external-link-alt" aria-hidden="true"></i>
        </a>
    @endif
</div>
