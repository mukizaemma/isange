@php
    $bookUrl = $bookUrl ?? route('room.booking');
    $discountPercent = $discountPercent ?? 30;
@endphp

<section class="isange-direct-discount" aria-label="Direct booking savings">
    <div class="container">
        <div class="isange-direct-discount__row">
            <div class="isange-direct-discount__col">
                <span class="isange-direct-discount__icon" aria-hidden="true">
                    <i class="fas fa-shield-alt"></i>
                </span>
                <div class="isange-direct-discount__body">
                    <p class="isange-direct-discount__text">
                        Save Up to <strong>{{ $discountPercent }}%</strong> When You Book Direct
                    </p>
                    <a href="{{ $bookUrl }}" class="isange-direct-discount__btn">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        Unlock Now
                    </a>
                </div>
            </div>

            <div class="isange-direct-discount__col">
                <span class="isange-direct-discount__icon" aria-hidden="true">
                    <i class="fas fa-tag"></i>
                </span>
                <div class="isange-direct-discount__body isange-direct-discount__body--inline">
                    <p class="isange-direct-discount__text">
                        Up to <strong>{{ $discountPercent }}%</strong> Lower Than OTA Prices
                    </p>
                    <a href="{{ $bookUrl }}" class="isange-direct-discount__btn">
                        Check Now
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="isange-direct-discount__col">
                <span class="isange-direct-discount__icon" aria-hidden="true">
                    <i class="fas fa-gift"></i>
                </span>
                <div class="isange-direct-discount__body">
                    <p class="isange-direct-discount__text isange-direct-discount__text--emphasis">
                        Exclusive Perks Reserved For Direct Guests
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
