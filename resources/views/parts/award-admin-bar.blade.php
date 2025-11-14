@if($can('awards_edit') || $can('nominations_view') || $can('autocompleter_edit'))
<nav class="navbar navbar-expand-md navbar-light bg-yotsuba admin-navbar">
    <div class="container">
        <a class="navbar-brand me-auto" href="{{ route('awards') }}">Awards and Nominations</a>
        <ul class="navbar-nav">
            @can('awards_edit')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('awards.manage') }}">Manage awards</a>
            </li>
            @endcan
            @can('nominations_view')
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Manage nominees</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('nominees.manage') }}">Nominee manager</a>
                    <a class="dropdown-item" href="{{ route('tasks') }}">Missing nominee data</a>
                    <a class="dropdown-item" href="{{ route('tasks.check-images') }}">Image optimisation check</a>
                </div>
            </li>
            @endcan
            @can('autocompleter_edit')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('autocompleters') }}">Manage autocompleters</a>
            </li>
            @endcan
        </ul>
    </div>
</nav>
@endif
