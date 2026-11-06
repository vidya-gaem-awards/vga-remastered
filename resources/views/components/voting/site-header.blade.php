@props(['text', 'logoPath'])

<header class="site-header">
    <a class="site-header__logo" href="{{ route('index') }}">
        <img class="site-header__logo-image" src="{{ asset($logoPath) }}" alt="/v/GAs">
    </a>
    <div class="site-header__sign">
        <p class="site-header__text">{{ $text }}</p>
    </div>
</header>
    