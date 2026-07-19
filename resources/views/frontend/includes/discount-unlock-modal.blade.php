@unless (auth()->user()?->hasUnlockedDiscount())
<div class="modal fade isange-discount-modal" id="unlockDiscountModal" tabindex="-1" aria-labelledby="unlockDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content isange-discount-modal__content">
            <button type="button" class="btn-close isange-discount-modal__close" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="isange-discount-modal__hero">
                <span class="isange-discount-modal__icon"><i class="fas fa-tag" aria-hidden="true"></i></span>
                <span class="isange-section__eyebrow">Direct booking reward</span>
                <h2 id="unlockDiscountModalLabel">Unlock your room discount</h2>
                <p>Enter your email and we’ll send a secure 4-digit code. New and returning guests use the same quick process.</p>
            </div>

            <div class="isange-discount-modal__body">
                <div class="alert d-none isange-discount-modal__status" id="discount-modal-status" role="alert"></div>

                <form id="discount-email-form" novalidate>
                    <label for="discount-modal-email">Email address</label>
                    <div class="isange-discount-modal__input-wrap">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <input id="discount-modal-email" type="email" name="email" required autocomplete="email" placeholder="you@email.com">
                    </div>
                    <label class="isange-discount-modal__consent" for="discount-modal-marketing">
                        <input id="discount-modal-marketing" type="checkbox" name="marketing_opt_in" value="1">
                        <span>Send me occasional hotel news and offers. I can unsubscribe anytime.</span>
                    </label>
                    <button class="theme-btn isange-discount-modal__submit" type="submit">
                        <span>Send my 4-digit code</span> <i class="far fa-angle-right"></i>
                    </button>
                </form>

                <form id="discount-code-form" class="d-none" novalidate>
                    <p class="isange-discount-modal__sent">Code sent to <strong id="discount-modal-masked-email"></strong></p>
                    <label class="visually-hidden" for="discount-modal-code">4-digit code</label>
                    <input
                        class="isange-discount-modal__code"
                        id="discount-modal-code"
                        name="code"
                        inputmode="numeric"
                        pattern="[0-9]{4}"
                        maxlength="4"
                        required
                        autocomplete="one-time-code"
                        placeholder="••••"
                    >
                    <p class="isange-discount-modal__attempts">You have up to 3 attempts.</p>
                    <button class="theme-btn isange-discount-modal__submit" type="submit">
                        <span>Validate &amp; apply discount</span> <i class="fas fa-check"></i>
                    </button>
                    <button class="isange-discount-modal__resend" id="discount-modal-resend" type="button">Send a new code</button>
                </form>

                <a class="isange-discount-modal__continue" href="{{ route('booking.checkout') }}">Continue without discount</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('unlockDiscountModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    var emailForm = document.getElementById('discount-email-form');
    var codeForm = document.getElementById('discount-code-form');
    var emailInput = document.getElementById('discount-modal-email');
    var marketingInput = document.getElementById('discount-modal-marketing');
    var codeInput = document.getElementById('discount-modal-code');
    var maskedEmail = document.getElementById('discount-modal-masked-email');
    var status = document.getElementById('discount-modal-status');
    var resend = document.getElementById('discount-modal-resend');
    var requestUrl = @json(route('guest.discount.code.request'));
    var verifyUrl = @json(route('guest.discount.code.verify'));
    var unlockUrl = @json(route('guest.discount'));
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function message(text, type) {
        status.className = 'alert isange-discount-modal__status alert-' + type;
        status.textContent = text;
    }

    function clearMessage() {
        status.className = 'alert d-none isange-discount-modal__status';
        status.textContent = '';
    }

    function responseMessage(data, fallback) {
        if (data && data.errors) {
            var first = Object.values(data.errors)[0];
            if (Array.isArray(first) && first[0]) return first[0];
        }
        return (data && data.message) || fallback;
    }

    function setBusy(form, busy) {
        var button = form.querySelector('button[type="submit"]');
        if (!button) return;
        if (!busy && form.dataset.locked === '1') {
            button.disabled = true;
            button.classList.remove('is-loading');
            return;
        }
        button.disabled = busy;
        button.classList.toggle('is-loading', busy);
    }

    async function sendCode() {
        if (!emailInput.reportValidity()) return;
        clearMessage();
        setBusy(emailForm, true);

        try {
            var response = await fetch(requestUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    email: emailInput.value.trim(),
                    marketing_opt_in: marketingInput.checked
                })
            });
            var data = await response.json();
            if (!response.ok) throw new Error(responseMessage(data, 'Unable to send the code.'));

            maskedEmail.textContent = data.email;
            emailForm.classList.add('d-none');
            codeForm.classList.remove('d-none');
            codeInput.value = '';
            message(data.message, 'success');
            setTimeout(function () { codeInput.focus(); }, 120);
        } catch (error) {
            message(error.message, 'danger');
        } finally {
            setBusy(emailForm, false);
        }
    }

    document.addEventListener('click', function (event) {
        var link = event.target.closest('a[href="' + unlockUrl + '"], [data-unlock-discount]');
        if (!link) return;
        event.preventDefault();
        clearMessage();
        modal.show();
        setTimeout(function () { emailInput.focus(); }, 180);
    });

    emailForm.addEventListener('submit', function (event) {
        event.preventDefault();
        sendCode();
    });

    resend.addEventListener('click', function () {
        delete codeForm.dataset.locked;
        codeInput.disabled = false;
        codeForm.classList.add('d-none');
        emailForm.classList.remove('d-none');
        sendCode();
    });

    codeForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!codeInput.reportValidity()) return;
        clearMessage();
        setBusy(codeForm, true);

        try {
            var response = await fetch(verifyUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ code: codeInput.value })
            });
            var data = await response.json();
            if (!response.ok) {
                message(responseMessage(data, 'The code could not be verified.'), 'danger');
                if (data.locked) {
                    codeForm.dataset.locked = '1';
                    codeInput.disabled = true;
                    codeForm.querySelector('button[type="submit"]').disabled = true;
                }
                return;
            }

            message(data.message, 'success');
            modal.hide();
            setTimeout(function () { window.location.reload(); }, 350);
        } catch (error) {
            message(error.message, 'danger');
        } finally {
            setBusy(codeForm, false);
        }
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        clearMessage();
        codeInput.disabled = false;
        delete codeForm.dataset.locked;
        emailForm.classList.remove('d-none');
        codeForm.classList.add('d-none');
        codeInput.value = '';
    });
});
</script>
@endunless
