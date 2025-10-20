@php $locale = session()->get('locale'); @endphp

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
    <div id="global">
        <header id="headerSite">
            <a href="{{ url('/') }}">
                <h1 id="titreSite">Ventique</h1>
            </a>
            
            <p id="descSite">Bienvenue au enchère d'objets antiques</p>
        </header>
                        @if (Auth::user())
                            @if (Auth::user()->role === 'USER')
                                <a class="navbar-brand" href="{{ url('/') }}">
                                    {{ config('app.name') }}
                                </a>
                            @endif
                        @endif
        <div id="antique-search-container">
            <form method="GET" action="{{ url('/') }}">
                <div class="form-group">
                    <input type="text" name="search" class="antique-searchbar typeahead form-control"
                        id="antique_search" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
            </form>

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
                            // put label in input and submit the GET form so index is filtered
                            if (ui.item && ui.item.label) {
                                $('#antique_search').val(ui.item.label);
                                $('#antique_search').closest('form').submit();
                            }
                            return false;
                        },
                        minLength: 2
                    });
                });
                
                @php $locale = session()->get('locale'); @endphp
                <div class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        @switch($locale)
                            @case('en')
                                English
                                @break
                            @case('fr')
                                Français
                                @break
                            @case('es')
                                Español
                                @break
                            @default
                            English
                        @endswitch
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="lang/en">English</a>
                        <a class="dropdown-item" href="lang/fr">Français</a>
                        <a class="dropdown-item" href="lang/es">Español</a>
                    </div>
                </div>

            </script>
        </div>
        <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->

            @if (Auth::user()) {{-- accées au boutons d"enregistrement de connéction et de déconnexion peu importe le rôle de l'utilisateur authentifié --}}
                @if (Auth::user()->role === 'ADMIN')
                    {{-- Accées à l'espace admin Juste pour les ADMIN --}}
                            <li class="nav-item">
                                <a class="nav-link" href ="{{ route('articles.index') }}"> Espace Admin</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                          document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Connexion') }}</a>
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Inscription') }}</a>

                    @endif
                </ul>
        <div id="contenu">
            @yield('content')
        </div>

        <div id="filler"></div>

        <footer id="footer">
            <p>Ventique &copy; 2025</p>
            <a style="color: rgb(236, 203, 159);" href="{{ url('/apropos') }}">
                <p>À propos</p>
            </a>
        </footer>
    </div>
</body>

</html>