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

        <div class="car-body">
            <form method="GET" action="{{ url('/') }}">
                <div class="form-group">
                    <input type="text" name="search" class="typeahead form-control" id="antique_search"
                        placeholder="Rechercher..." value="{{ request('search') }}">
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
            </script>
        </div>

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