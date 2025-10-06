<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/src/style.css" rel="stylesheet">
</head>

<body>
    <div id="global">
        <header id="headerSite">
            <a href="">
                <h1 id="titreSite">Ventique</h1>
            </a>
            <p id="descSite">Bienvenue au enchère d'objets antiques</p>
            <?php if (isset($_SESSION['utilisateur'])): ?>
            <a href="AdminOffres/index">
                <h4>Afficher toutes les offres de tous les articles</h4>
            </a>
            <?php endif; ?>
            <div id="connexion">
                <?php if (isset($_SESSION['utilisateur'])): ?>
                <a href="Utilisateurs/deconnecter">
                    <h3>Bonjour <?= $_SESSION['utilisateur']['nom'] ?>,
                        <small>Se déconnecter</small>
                </a>
                </h3>
                <?php else: ?>
                <a href="Utilisateurs/index">
                    <h3>Se connecter<small></small></h3>
                    <?php endif; ?>
            </div>
        </header>
        <div class="car-body">
            <form>
                @csrf
                <div class="form-group">
                    <input type="text" class="typeahead form-control" id="article_search" placeholder="Rechercher...">
                </div>
            </form>
            <script type="text/javascript">
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $(document).ready(function () {
                    $('#antique_search').autocomplete({
                        source: function (request, response) {
                            $.ajax({
                                url: "{{route('autocomplete')}}",
                                type: 'POST',
                                dataType: "json",
                                data: {
                                    _token: CSRF_TOKEN,
                                    search: request.term
                                },
                                success: function (data) {
                                    response(data);
                                }
                            });
                        },
                        select: function (event, ui) {
                            $('#antique_search').val(ui.item.label);

                            return false;
                        }
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
            <a style="color: rgb(236, 203, 159);" href="Apropos/index">
                <p>À propos</p>
            </a>
        </footer>
    </div>
    <!-- <div class="bg-blue-200 flex w-max h-2.5">
        <h2 class="">Ventique</h2>
    </div>
    <button class="bg-sky-500/100 ">Temp</button>
    <div class="background">
        @yield('content')
    </div> -->
</body>

</html>