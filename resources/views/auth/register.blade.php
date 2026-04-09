@extends('layouts.guest')

@section('title', 'Créer un compte')

@section('content')
    <form class="card" action="{{ route('register') }}" method="POST" id="accountCreate">
        @csrf
        <h2>Créer un compte</h2>

        <input id="firstName"
               type="text"
               class="textzone"
               name="prenom"
               placeholder="Prénom"
               value="{{ old('prenom') }}">
        <div id="firstName_error" class="error-text titanic">
            Veuillez renseigner votre prénom.
        </div>
        <br><br>

        <input id="lastName"
               type="text"
               class="textzone"
               name="nom"
               placeholder="Nom"
               value="{{ old('nom') }}">
        <div id="lastName_error" class="error-text titanic">
            Veuillez renseigner votre nom.
        </div>
        <br><br>

        <input id="email"
               type="email"
               class="textzone"
               name="email"
               placeholder="Email"
               value="{{ old('email') }}">
        <div id="email-creator_error" class="error-text titanic">
            Veuillez renseigner un email valide.
        </div>
        <br><br>

        <input id="password"
               type="password"
               class="textzone"
               name="password"
               placeholder="Mot de passe">
        <div id="password_error" class="error-text titanic">
            Veuillez renseigner un mot de passe valide.
        </div>
        <br><br>

        <button type="submit" class="enter">Enregistrer</button>
    </form>
@endsection
