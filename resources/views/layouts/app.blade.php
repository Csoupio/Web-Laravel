<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ticketing')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/ticket-template.css') }}">
    <link rel="stylesheet" href="{{ asset('css/time-tracking.css') }}">
    @stack('styles')
</head>
<body class="@yield('body-class')">

    <header>
        <div class="bandeau">
            <div class="start">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/logo.png') }}" class="Logo" alt="Logo">
                </a>
            </div>
            <div class="middle">
                @yield('header-title', 'Ticketing')
            </div>
            <div class="end">
                <button id="loginmenu" class="login" type="button">Votre Compte</button>
                <div id="dropdownMenu" class="dropdown-content">
                    <a href="{{ route('projets.index') }}" class="menu-item">Mes projets</a>
                    @if(Auth::check() && Auth::user()->role === 'Administrateur')
                    <a href="{{ route('admin.index') }}" class="menu-item">Espace administrateur</a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="menu-item">Dashboard</a>
                    <a href="{{ route('facturation.validation.index') }}" class="menu-item">Validation facturation</a>
                    <a href="{{ route('logout') }}" class="menu-item">Se déconnecter</a>
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
