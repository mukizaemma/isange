@php
    $showCategoryChip = $showCategoryChip ?? false;
@endphp
<div class="col-md-6 col-xl-4 js-dining-menu-col wow fadeInUp" data-menu-cat="{{ $item->menu_category_id ?? '0' }}">
    <article class="dining-dish-card h-100">
        <div class="dining-dish-card__media">
            @if ($item->image)
                <img src="{{ asset('storage/images/dining/'.$item->image) }}" alt="{{ $item->title }}" loading="lazy">
            @else
                <div class="dining-dish-card__placeholder d-flex align-items-center justify-content-center" style="color: rgba(255,255,255,0.55);">Isange</div>
            @endif
        </div>
        <div class="dining-dish-card__body">
            @if ($showCategoryChip && $item->category)
                <span class="dining-dish-card__cat">{{ $item->category->name }}</span>
            @endif
            <h3 class="dining-dish-card__title">{{ $item->title }}</h3>
            @if (! empty($item->description))
                <p class="dining-dish-card__desc">{!! nl2br(e($item->description)) !!}</p>
            @endif
            <div class="dining-dish-card__meta d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <span class="dining-dish-card__price">{!! \App\Support\Currency::formatUsdWithLocal($item->price_usd, $item->price_rwf) !!}</span>
                <button type="button"
                    class="theme-btn style-three btn-sm dining-dish-add"
                    data-id="{{ $item->id }}"
                    data-title="{{ e($item->title) }}"
                    data-price="{{ number_format($item->price_usd, 2, '.', '') }}"
                    data-price-rwf="{{ $item->price_rwf && (float) $item->price_rwf > 0 ? number_format((float) $item->price_rwf, 0, '', '') : '' }}">
                    <i class="fas fa-plus me-1"></i> Quick add
                </button>
            </div>
        </div>
    </article>
</div>
