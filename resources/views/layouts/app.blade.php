


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- jQuery and jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">

    <!-- Your styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <!-- Debug info - remove after testing -->
    <div style="position: fixed; top: 0; right: 0; background: red; color: white; padding: 5px; z-index: 9999; font-size: 12px;">
        App Locale: {{ App::getLocale() }}<br>
        Session: {{ session()->get('locale', 'not set') }}
    </div>
    
    <div id="global">
        <header id="headerSite">
            <div class="header-top">
                <a href="{{ url('/') }}">
                    <h1 id="titreSite">Ventique</h1>
                </a>
                <p id="descSite">@lang("header_desc")</p>
            </div>

            <nav class="header-nav">
                <!-- Search Bar -->
                <div id="antique-search-container">
                    <form method="GET" action="{{ url('/') }}">
                        <div class="form-group">
                            <input type="text" name="search" class="antique-searchbar typeahead form-control"
                                id="antique_search" placeholder="{{ __('index_recherche_placeholder') }}" value="{{ request('search') }}">
                        </div>
                    </form>
                </div>

                <!-- Authentication Links -->
                <ul class="navbar-nav">
                    @if (Auth::user())
                        @if (Auth::user()->role === 'ADMIN')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('articles.index') }}">Espace Admin</a>
                            </li>
                        @endif
                        
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button">
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Déconnexion') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Connexion') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Inscription') }}</a>
                        </li>
                    @endif
                </ul>

                <!-- Language Dropdown -->
                <div class="nav-item dropdown">
                    <a id="languageDropdown" class="nav-link dropdown-toggle" href="#" role="button">
                        @switch(App::getLocale())
                            @case('en')
                                🇺🇸 English
                                @break
                            @case('fr')
                                🇫🇷 Français
                                @break
                            @case('es')
                                🇪🇸 Español
                                @break
                            @default
                                🇺🇸 English
                        @endswitch
                    </a>
                    <div class="dropdown-menu" aria-labelledby="languageDropdown">
                        <a class="dropdown-item" href="{{ url('lang/en') }}">🇺🇸 English</a>
                        <a class="dropdown-item" href="{{ url('lang/fr') }}">🇫🇷 Français</a>
                        <a class="dropdown-item" href="{{ url('lang/es') }}">🇪🇸 Español</a>
                    </div>
                </div>
            </nav>
        </header>

        <script type="text/javascript">
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $(function () {
                $('#antique_search').autocomplete({
                    source: function (request, response) {
                        $.ajax({
                            url: "{{ route('autocomplete') }}",
                            type: 'POST',
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function (data) {
                                response(data);
                            },
                            error: function (xhr, status, error) {
                                console.error('Autocomplete error:', status, error, xhr.responseText);
                                response([]);
                            }
                        });
                    },
                    select: function (event, ui) {
                        if (ui.item && ui.item.label) {
                            $('#antique_search').val(ui.item.label);
                            $('#antique_search').closest('form').submit();
                        }
                        return false;
                    },
                    minLength: 2
                });
            });
        </script>

        <div id="contenu">
            @yield('content')
        </div>

        <div id="filler"></div>

        <footer id="footer">
            <p>Ventique &copy; 2025</p>
            <a style="color: rgb(236, 203, 159);" href="{{ url('/apropos') }}">
                <p>@lang("footer_apropos")</p>
            </a>
        </footer>
    </div>
</body>

</html>