@php
    $bookingContent = \App\Support\PageContent::get('booking', $pageHeaders ?? collect());
    $benefitsHeading = $bookingContent['sections']['benefits_heading'] ?? 'Benefits of booking with us';
    $benefitsLines = $bookingContent['sections']['benefits_lines'] ?? [];
@endphp

@if ($benefitsHeading !== '' || count($benefitsLines) > 0)
    <section class="ma-booking-benefits" aria-labelledby="booking-benefits-title">
        <div class="ma-booking-benefits__card">
            @if ($benefitsHeading !== '')
                <header class="ma-booking-benefits__head">
                    <h2 class="ma-booking-benefits__title" id="booking-benefits-title">{{ $benefitsHeading }}</h2>
                </header>
            @endif

            @if (count($benefitsLines) > 0)
                <ul class="ma-booking-benefits__grid">
                    @foreach ($benefitsLines as $line)
                        @php
                            $text = is_array($line) ? ($line['text'] ?? '') : (string) $line;
                            $type = is_array($line) ? ($line['type'] ?? 'bullet') : 'bullet';
                        @endphp
                        @if (trim($text) === '')
                            @continue
                        @endif
                        @if ($type === 'note')
                            <li class="ma-booking-benefits__item ma-booking-benefits__item--note">
                                <i class="fas fa-info-circle" aria-hidden="true"></i>
                                <span>{{ $text }}</span>
                            </li>
                        @else
                            <li class="ma-booking-benefits__item">
                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                <span>{{ $text }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endif
