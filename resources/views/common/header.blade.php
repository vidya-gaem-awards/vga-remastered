<header>
    <a class="logo" href="{{ route('index') }}">
        <picture>
            <img src="{{ asset('2025images/vga_logo_2.webp') }}">
        </picture>
    </a>
    <div class="right-container">
        <div class="title-text">
            {{ $text ?? year() . " Vidya Game Awards" }}
        </div>
    </div>
</header>