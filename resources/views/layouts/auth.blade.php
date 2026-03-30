<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Connexion')</title>
    <link rel="stylesheet" href="{{ asset('css/ticket-template.css') }}">
</head>
<body class="background">

    <a href="{{ url('/') }}">
        <img src="{{ asset('assets/logo.png') }}" class="Logo" alt="Logo">
    </a>

    <div class="corp">
        @yield('content')
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
