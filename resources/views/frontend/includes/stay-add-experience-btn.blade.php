{{-- Requires: $expId, $expTitle; optional $expIcon, $btnClass --}}
<button type="button"
    class="btn btn-sm stay-add-exp-btn {{ $btnClass ?? 'btn-outline-success' }}"
    data-add-experience="{{ $expId }}"
    data-exp-title="{{ $expTitle }}"
    data-exp-icon="{{ $expIcon ?? 'fa-star' }}"
    aria-pressed="false">
    <i class="fas fa-plus-circle me-1" aria-hidden="true"></i>
    <span data-add-label>Add to itinerary</span>
</button>
