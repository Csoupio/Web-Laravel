<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil — Ticketing</title>
    <link rel="stylesheet" href="{{ asset('css/ticket-template.css') }}">
</head>
<body>
    <header>
        <div class="bandeau">
            <div class="start">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/logo.png') }}" class="Logo" alt="Logo">
                </a>
            </div>
            <div class="middle">Bienvenue</div>
            <div class="end">
                <button class="login" type="button"
                    onclick="window.location.href='{{ route('register') }}'">
                    Créer un compte
                </button>
                <button class="login" type="button"
                    onclick="window.location.href='{{ route('login') }}'">
                    Se connecter
                </button>
            </div>
        </div>
    </header>
</body>
</html>
