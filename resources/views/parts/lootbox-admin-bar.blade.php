<nav class="navbar navbar-expand-md navbar-light bg-yotsuba admin-navbar">
    <div class="container">
        <a class="navbar-brand me-auto" href="{{ route('lootbox.items') }}">Lootboxes</a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('lootbox.items') }}">Manage items</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('lootbox.tiers') }}">Manage tiers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('lootbox.settings') }}">Lootbox settings</a>
            </li>
        </ul>
    </div>
</nav>
