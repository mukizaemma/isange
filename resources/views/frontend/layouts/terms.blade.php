<section class="services-area-four pt-80 rpt-60 pb-90 rpb-60 rel z-2">
    <div class="container">
        <div class="row gap-80 justify-content-between align-items-center">
            <div class="col-lg-5">
                <div class="activity-left-content mb-40 rmb-55 wow fadeInUp delay-0-2s">
                    <div class="payment-details wow fadeInUp delay-0-3s">
                        <h2>Our Payment Methods</h2>
                        <p>We accept payments through the following bank accounts:</p>
                        <ul class="payment-list">
                            <li><i class="fas fa-university"></i> <strong>Bank Account (RWF - Equity):</strong> 4032200030584</li>
                            <li><i class="fas fa-dollar-sign"></i> <strong>Bank Account (USD - Equity):</strong> 4032200030708</li>
                            <li><i class="fas fa-globe"></i> <strong>SWIFT Code:</strong> EQBLRWRW</li>
                            <li><i class="fas fa-globe"></i> <strong>Momo Pay:</strong> Contact resort for current payment details</li>
                        </ul>
                        <p><strong>For payment-related inquiries, contact our support team:</strong></p>
                        <ul class="contact-list">
                            <li><i class="fas fa-phone-alt"></i> Phone: <a href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a></li>
                            <li><i class="fas fa-envelope"></i> Email: <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row gap-50">
                    <div class="col-lg-4 col-6 col-small">
                        <div class="service-item style-two wow fadeInUp delay-0-3s">
                            <div class="icon">
                                <i class="fas fa-ban"></i> <!-- Cancellation Icon -->
                            </div>
                            <div class="content">
                                <h4><a href="{{ route('rooms') }}">Cancellation Policy</a></h4>
                                <p>Free cancellation within 5 days.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6 col-small">
                        <div class="service-item style-two wow fadeInUp delay-0-4s">
                            <div class="icon">
                                <i class="fas fa-laptop-house"></i> <!-- Booking Icon -->
                            </div>
                            <div class="content">
                                <h4><a href="{{ route('rooms') }}">Booking Channels</a></h4>
                                <p>Bookings are accepted only via our website, Booking.com, or listed partners.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6 col-small">
                        <div class="service-item style-two wow fadeInUp delay-0-5s">
                            <div class="icon">
                                <i class="fas fa-credit-card"></i> <!-- Payment Icon -->
                            </div>
                            <div class="content">
                                <h4><a href="{{ route('rooms') }}">Payment Options</a></h4>
                                <p>Payments are only accepted through our official accounts or in person.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>