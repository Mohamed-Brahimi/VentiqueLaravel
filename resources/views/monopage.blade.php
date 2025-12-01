<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>Ventique - Enchères d'Antiquités</title>
        
        <!-- Vite assets - this loads your Vue app -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Google reCAPTCHA -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>

    <body>
        @if (Auth::check()) 
            @php
                $user_auth_data = [
                    'isLoggedIn' => true,
                    'user' => Auth::user(),
                ];
            @endphp
        @else
            @php
                $user_auth_data = [
                    'isLoggedIn' => false,
                ]; 
            @endphp
        @endif
        
        <script>
            window.Laravel = JSON.parse(atob('{{ base64_encode(json_encode($user_auth_data)) }}'));
            window.Laravel.csrfToken = '{{ csrf_token() }}';
            window.RECAPTCHA_SITE_KEY = '{{ env('VITE_RECAPTCHA_SITE_KEY') }}';
        </script>

        <!-- Vue app mounts here -->
        <div id="app"></div>
    </body>
</html>