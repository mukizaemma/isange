@php
    $variant = $variant ?? 'button';
    $class = trim(($class ?? '').' ma-guest-logout');
@endphp
@auth
    @if (auth()->user()->isGuest())
        <form method="POST" action="{{ route('logout') }}" class="{{ $class }}">
            @csrf
            @if ($variant === 'icon')
                <button type="submit" class="ma-guest-logout__icon" title="Log out" aria-label="Log out">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                </button>
            @else
                <button type="submit" class="theme-btn btn-sm style-three ma-guest-logout__btn" title="Log out">
                    <i class="fas fa-sign-out-alt me-1" aria-hidden="true"></i> Log out
                </button>
            @endif
        </form>
    @endif
@endauth
