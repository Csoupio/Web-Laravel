
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue | Ticketing Pro</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
</head>
<body class="home-page">

    {{-- ═══ NAVIGATION ═══ --}}
    <nav class="home-nav">
        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="home-logo">
        <div class="cta-group">
            <a href="{{ route('login') }}" class="btn-outline">Connexion</a>
            <a href="{{ route('register') }}" class="btn-primary">Créer un compte</a>
        </div>
    </nav>

    {{-- ═══ HERO SECTION ═══ --}}
    <header class="hero">
        <div class="hero-content">
            <h1>Optimisez la gestion de vos projets</h1>
            <p>La plateforme tout-en-un pour le suivi de vos tickets, la gestion du temps et la validation de facturation en toute transparence.</p>
            <div class="cta-group">
                <a href="{{ route('register') }}" class="btn-primary">Démarrer maintenant</a>
                <a href="#features" class="btn-outline">Découvrir les fonctionnalités</a>
            </div>
        </div>
        <div class="hero-visual">
            <img src="{{ asset('assets/hero_illustration.png') }}" alt="Support Dashboard Illustration">
        </div>
    </header>

    {{-- ═══ FEATURES SECTION ═══ --}}
    <section id="features" class="features">
        <div class="feature-card">
            <div class="feature-icon"></div>
            <h3>Gestion de projets</h3>
            <p>Organisez vos tickets par projet et gardez une vue d'ensemble sur l'avancement de vos équipes.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"></div>
            <h3>Suivi du temps</h3>
            <p>Enregistrez précisément le temps passé sur chaque tâche. Différenciez le temps inclus et le temps facturable.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"></div>
            <h3>Validation Client</h3>
            <p>Faites valider vos heures facturables directement par vos clients avant toute émission de facture.</p>
        </div>
    </section>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="home-footer">
        <p>&copy; {{ date('Y') }} Ticketing Pro. Tous droits réservés.</p>
    </footer>

</body>
</html>
