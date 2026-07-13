{{-- Shared admin discount fields. Expects optional $room for edit forms. --}}
@php
    $discountEnabled = (bool) old('discount_enabled', isset($room) ? $room->discount_enabled : false);
    $discountType = old('discount_type', isset($room) ? ($room->discount_type ?? 'percent') : 'percent');
    $discountValue = old('discount_value', isset($room) ? $room->discount_value : '');
@endphp
<div class="row mt-3 room-discount-fields" data-room-discount>
    <div class="col-12">
        <div class="form-check mb-2">
            <input type="hidden" name="discount_enabled" value="0">
            <input class="form-check-input" type="checkbox" value="1" id="discount_enabled_{{ $discountFieldId ?? 'create' }}"
                name="discount_enabled" data-discount-toggle
                @checked($discountEnabled)>
            <label class="form-check-label" for="discount_enabled_{{ $discountFieldId ?? 'create' }}">
                Show a discounted rate on this room
            </label>
        </div>
        <p class="small text-muted mb-2">Only rooms with this enabled display the promotional price (original struck through + sale price). Leave off for rooms at full rate.</p>
    </div>
    <div class="col-md-4" data-discount-controls @style([ 'display: none' => ! $discountEnabled ])>
        <label for="discount_type_{{ $discountFieldId ?? 'create' }}">Discount type</label>
        <select name="discount_type" id="discount_type_{{ $discountFieldId ?? 'create' }}" class="form-select @error('discount_type') is-invalid @enderror" data-discount-type>
            <option value="percent" @selected($discountType === 'percent')>Percentage (%)</option>
            <option value="fixed" @selected($discountType === 'fixed')>Fixed amount (USD)</option>
        </select>
        @error('discount_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4" data-discount-controls @style([ 'display: none' => ! $discountEnabled ])>
        <label for="discount_value_{{ $discountFieldId ?? 'create' }}" data-discount-value-label>
            {{ $discountType === 'fixed' ? 'Amount off (USD)' : 'Percent off (%)' }}
        </label>
        <input type="number" step="0.01" min="0.01" name="discount_value"
            id="discount_value_{{ $discountFieldId ?? 'create' }}"
            class="form-control @error('discount_value') is-invalid @enderror"
            value="{{ $discountValue }}"
            placeholder="{{ $discountType === 'fixed' ? 'e.g. 20' : 'e.g. 15' }}"
            data-discount-value>
        @error('discount_value')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
