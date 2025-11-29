<!DOCTYPE html>
<html>
    <head>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        </script>

        <div id="app"></div>
    </body>
</html>